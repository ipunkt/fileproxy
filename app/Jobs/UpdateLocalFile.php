<?php

namespace App\Jobs;

use App\Exceptions\LocalProxyFileCanNotByDeleted;
use App\LocalFile;
use App\ProxyFile;
use App\RemoteFile;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\UploadedFile;

class UpdateLocalFile
{
    use Dispatchable;

    /**
     * @var ProxyFile
     */
    private $proxyFile;

    /**
     * @var UploadedFile
     */
    private $file;

    /**
     * Create a new job instance.
     *
     * @param ProxyFile $proxyFile
     * @param UploadedFile $file
     */
    public function __construct(ProxyFile $proxyFile, UploadedFile $file)
    {
        $this->proxyFile = $proxyFile;
        $this->file = $file;
    }

    /**
     * Execute the job.
     *
     * @return void
     * @throws \App\Exceptions\LocalProxyFileCanNotByDeleted
     */
    public function handle()
    {
        /** @var Filesystem|\Illuminate\Filesystem\FilesystemAdapter $filesystem */
        $filesystem = app(Filesystem::class);

        //  remove previous file content

        /** @var LocalFile|RemoteFile $currentFile */
        $currentFile = $this->proxyFile->file;
        if (!$filesystem->delete($currentFile->getLocalStoragePath())) {
            throw LocalProxyFileCanNotByDeleted::throwException();
        }
        $currentFile->forceDelete();

        //  proceed new file content

        $fileHandle = $this->file->openFile();
        $content = $fileHandle->fread($this->file->getSize());

        $this->proxyFile->update([
            'type' => 'local',
            'filename' => $this->file->getClientOriginalName(),
            'mimetype' => $this->file->getClientMimeType(),
            'size' => $this->file->getSize(),
            'checksum' => sha1($content),
        ]);

        /** @var \App\LocalFile $localFile */
        $localFile = $this->proxyFile->localFile()->create([
            'path' => uniqid('', true),
        ]);

        if (!$filesystem->exists('local')) {
            $filesystem->makeDirectory('local');
        }

        $path = $localFile->getLocalStoragePath();
        if ($filesystem->put($path, $content)) {
            $localFile->path = $path;
            $localFile->save();
        }
    }
}
