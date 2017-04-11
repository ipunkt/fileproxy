<?php

namespace App\Jobs;

use App\Exceptions\RemoteFileNotAccessibleException;
use App\ProxyFile;
use App\RemoteFile;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;
use Mimey\MimeTypes;

class CreateRemoteFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var string
     */
    private $url;

    /**
     * @var array
     */
    private $options;

    /**
     * @var string
     */
    private $reference;

    public function __construct(string $reference, string $url, array $options = [])
    {
        $this->url = $url;
        $this->options = $options;
        $this->reference = $reference;
    }

    /**
     * @throws \App\Exceptions\RemoteFileNotAccessibleException
     */
    public function handle()
    {
        $client = new Client();
        $method = array_get($this->options, 'method', 'GET');
        $response = $client->request($method, $this->url, $this->options);
        if ($response->getStatusCode() > 299) {
            throw new RemoteFileNotAccessibleException($this->url);
        }

        $path = null;

        try {
            \DB::beginTransaction();

            $content = (string) $response->getBody();

            $filename = basename($this->url);
            $parts = explode('.', $filename);
            $extension = last($parts);

            $mimetype = new MimeTypes();

            $proxyFile = new ProxyFile([
                'reference' => $this->reference,
                'type' => 'remote',
                'filename' => $filename,
                'mimetype' => $mimetype->getMimeType($extension),
                'size' => Str::length($content),
                'checksum' => sha1($content),
            ]);
            $proxyFile->save();

            /** @var \App\RemoteFile $remoteFile */
            $remoteFile = $proxyFile->remoteFile()->create([
                'url' => $this->url,
                'options' => $this->options,
            ]);

            $this->cacheRemoteFileLocally($remoteFile, $content);

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            if ($path !== null) {
                @unlink(storage_path('app' . DIRECTORY_SEPARATOR . $path));
            }
            throw $exception;
        }
    }

    /**
     * cache remote file locally.
     *
     * @param \App\RemoteFile $remoteFile
     * @param string $content
     */
    private function cacheRemoteFileLocally(RemoteFile $remoteFile, string $content)
    {
        if (config('fileproxy.cache_remote_files', false) === false) {
            return;
        }

        /** @var Filesystem|\Illuminate\Filesystem\FilesystemAdapter $filesystem */
        $filesystem = app(Filesystem::class);
        if (! $filesystem->exists('remote')) {
            $filesystem->makeDirectory('remote');
        }

        $path = $remoteFile->getLocalStoragePath();
        if ($filesystem->put($path, $content)) {
            $remoteFile->path = $path;
            $remoteFile->save();
        }
    }
}
