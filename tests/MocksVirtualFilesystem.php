<?php

namespace Tests;

use League\Flysystem\Filesystem;
use League\Flysystem\Vfs\VfsAdapter;
use VirtualFileSystem\FileSystem as Vfs;
use Illuminate\Filesystem\FilesystemManager;

trait MocksVirtualFilesystem
{
    /**
     * mocks virtual filesystem.
     *
     * @return \League\Flysystem\Filesystem
     */
    protected function mockedVirtualFilesystem(): Filesystem
    {
        $storage = new FilesystemManager($this->app);

        $vfs = new Vfs();
        $vfs->createStructure([
            'remote' => [
            ],
        ]);

        $adapter = new VfsAdapter($vfs);
        $filesystem = new Filesystem($adapter);

        $storage->extend('vfs', function () use ($filesystem) {
            return $filesystem;
        });

        $this->app->instance(\Illuminate\Contracts\Filesystem\Filesystem::class, $storage);

        return $filesystem;
    }
}
