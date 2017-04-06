<?php

namespace App\Jobs;

use App\FileAlias;
use App\ServableFile;
use App\ServableRemoteFile;
use App\Interfaces\Sendable;
use App\Exceptions\FileAliasCanNotBeServed;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Filesystem\Filesystem;

class ServeFileAlias
{
    use Dispatchable;

    /**
     * @var string
     */
    private $aliasPath;
    /**
     * @var string
     */
    private $userAgent;

    public function __construct(string $aliasPath, string $userAgent = null)
    {
        $this->aliasPath = $aliasPath;
        $this->userAgent = $userAgent;
    }

    public function handle(): Sendable
    {
        $fileAlias = FileAlias::byPath($this->aliasPath);

        //  strategy check
        $this->validateFileAlias($fileAlias);

        /** @var \App\ProxyFile $proxyFile */
        $proxyFile = $fileAlias->proxyFile;

        $fileAlias->trackHit($this->userAgent);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $fileSystem */
        $fileSystem = app(Filesystem::class);

        $file = $proxyFile->file->path;
        if ($fileSystem->exists($file)) {
            return new ServableFile(
                config('filesystems.disks.'.config('filesystems.default').'.root').DIRECTORY_SEPARATOR.$file,
                $proxyFile->filename,
                $proxyFile->mimetype,
                $proxyFile->checksum
            );
        }

        if ($proxyFile->type === 'local') {
            abort(404);
        }

        return new ServableRemoteFile(
            $proxyFile->remoteFile,
            $proxyFile->filename,
            $proxyFile->mimetype,
            $proxyFile->checksum
        );
    }

    /**
     * validates visibility of an alias.
     *
     * @param \App\FileAlias $fileAlias
     *
     * @throws \App\Exceptions\FileAliasCanNotBeServed
     */
    private function validateFileAlias(FileAlias $fileAlias)
    {
        if (! $fileAlias->hitsLeft()) {
            throw FileAliasCanNotBeServed::noHitsLeft();
        }

        if (! $fileAlias->isValidNow()) {
            throw FileAliasCanNotBeServed::notVisible();
        }
    }
}
