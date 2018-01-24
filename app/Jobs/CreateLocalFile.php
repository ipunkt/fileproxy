<?php

namespace App\Jobs;

use App\ProxyFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;

class CreateLocalFile
{
	use Dispatchable;

	/**
	 * @var \Illuminate\Http\UploadedFile
	 */
	private $file;

	/**
	 * @var string
	 */
	private $reference;

	public function __construct(string $reference, UploadedFile $file)
	{
		$this->reference = $reference;
		$this->file = $file;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$fileHandle = $this->file->openFile();
		$content = $fileHandle->fread($this->file->getSize());

		$proxyFile = new ProxyFile([
			'reference' => $this->reference,
			'type' => 'local',
			'filename' => $this->file->getClientOriginalName(),
			'mimetype' => $this->file->getClientMimeType(),
			'size' => $this->file->getSize(),
			'checksum' => sha1($content),
		]);
		$proxyFile->save();

		/** @var \App\LocalFile $localFile */
		$localFile = $proxyFile->localFile()->create([
			'path' => uniqid('', true),
		]);

		/** @var Filesystem|\Illuminate\Filesystem\FilesystemAdapter $filesystem */
		$filesystem = app(Filesystem::class);

		if (!$filesystem->exists('local')) {
			$filesystem->makeDirectory('local');
		}

		$path = $localFile->getLocalStoragePath();

		$fh = fopen($this->file->getRealPath(), 'rb');
		if ($filesystem->put($path, $fh)) {
			$localFile->path = $path;
			$localFile->save();
		}
		fclose($fh);
	}
}
