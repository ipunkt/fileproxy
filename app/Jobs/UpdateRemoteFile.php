<?php

namespace App\Jobs;

use App\Exceptions\LocalProxyFileCanNotByDeleted;
use App\Exceptions\RemoteFileNotAccessibleException;
use App\LocalFile;
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

class UpdateRemoteFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var ProxyFile
     */
    private $proxyFile;
    /**
     * @var string
     */
    private $url;
    /**
     * @var array
     */
    private $options;

    /**
     * Create a new job instance.
     *
     * @param ProxyFile $proxyFile
     * @param string $url
     * @param array $options
     */
    public function __construct(ProxyFile $proxyFile, string $url, array $options = [])
    {
        $this->proxyFile = $proxyFile;
        $this->url = $url;
        $this->options = $options;
    }

    /**
     * Execute the job.
     * @throws \App\Exceptions\RemoteFileNotAccessibleException
     * @throws \Exception
     * @throws \App\Exceptions\LocalProxyFileCanNotByDeleted
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

        //  remove previous file content

        /** @var LocalFile|RemoteFile $currentFile */
        $currentFile = $this->proxyFile->file;
        if (!$this->getFilesystem()->delete($currentFile->getLocalStoragePath())) {
            throw LocalProxyFileCanNotByDeleted::throwException();
        }
        $currentFile->forceDelete();

        //  proceed new file content

        try {
            \DB::beginTransaction();

            $content = (string)$response->getBody();

            $filename = basename($this->url);
            $parts = explode('.', $filename);
            $extension = last($parts);

            $mimetype = new MimeTypes();

            $this->proxyFile->update([
                'type' => 'remote',
                'filename' => $filename,
                'mimetype' => $mimetype->getMimeType($extension),
                'size' => Str::length($content),
                'checksum' => sha1($content),
            ]);

            /** @var \App\RemoteFile $remoteFile */
            $remoteFile = $this->proxyFile->remoteFile()->create([
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

        $path = $remoteFile->getLocalStoragePath();
        if ($this->getFilesystem()->put($path, $content)) {
            $remoteFile->path = $path;
            $remoteFile->save();
        }
    }

    /**
     * sets up filesystem
     *
     * @return Filesystem|\Illuminate\Filesystem\FilesystemAdapter
     */
    private function getFilesystem()
    {
        /** @var Filesystem|\Illuminate\Filesystem\FilesystemAdapter $filesystem */
        $filesystem = app(Filesystem::class);
        if (!$filesystem->exists('remote')) {
            $filesystem->makeDirectory('remote');
        }
        return $filesystem;
    }
}
