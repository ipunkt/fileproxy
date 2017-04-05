<?php

namespace Tests;

use Illuminate\Filesystem\FilesystemManager;
use League\Flysystem\Filesystem;
use League\Flysystem\Vfs\VfsAdapter;
use VirtualFileSystem\FileSystem as Vfs;

trait MocksVirtualFilesystem
{
    /**
     * mocks virtual filesystem
     *
     * @return \League\Flysystem\Filesystem
     */
    protected function mockedVirtualFilesystem(): Filesystem
    {
        $storage = new FilesystemManager($this->app);

        $vfs = new Vfs();
        $vfs->createStructure([
            'remote' => [
            ]
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