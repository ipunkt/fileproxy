<?php

namespace Tests\Feature;

use App\RemoteFile;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use App\Jobs\CreateRemoteFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CreateRemoteFileTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_a_remote_file_with_dispatching_job()
    {
        // ARRANGE
        config(['fileproxy.cache_remote_files' => true]);
        $filesystem = $this->mockedVirtualFilesystem();

        $url = 'https://cdn.polyfill.io/v2/polyfill.min.js';
        $options = [];
        $reference = Uuid::uuid4();

        // ACT
        dispatch(new CreateRemoteFile($reference, $url, $options));

        // ASSERT
        $this->assertDatabaseHas('proxy_files', [
            'reference' => $reference,
            'type' => 'remote',
            'filename' => 'polyfill.min.js',
            'mimetype' => 'application/javascript',
        ]);

        $this->assertDatabaseHas('remote_files', [
            'url' => $url,
            'options' => null,
            'proxy_file_id' => 1,
            //  sha1(1) => 356a192b7913b04c54574d18c28d46e6395428ab
            'path' => 'remote/35/6a/356a192b7913b04c54574d18c28d46e6395428ab',
//            'path' => null,
        ]);
        $remoteFile = RemoteFile::first();
        $this->assertTrue($filesystem->has($remoteFile->path));
    }

    /** @test */
    public function it_can_create_a_remote_file_which_is_not_cached_locally_when_dispatching_job()
    {
        // ARRANGE
        $beforeValue = config('fileproxy.cache_remote_files');
        config(['fileproxy.cache_remote_files' => false]);
        $filesystem = $this->mockedVirtualFilesystem();

        $url = 'https://cdn.polyfill.io/v2/polyfill.min.js';
        $options = [];
        $reference = Uuid::uuid4();

        // ACT
        dispatch(new CreateRemoteFile($reference, $url, $options));

        // ASSERT
        $this->assertDatabaseHas('proxy_files', [
            'reference' => $reference,
            'type' => 'remote',
            'filename' => 'polyfill.min.js',
            'mimetype' => 'application/javascript',
        ]);

        $this->assertDatabaseHas('remote_files', [
            'url' => $url,
            'options' => null,
            'proxy_file_id' => 1,
            'path' => null,
        ]);
        $remoteFile = RemoteFile::first();
        $this->assertFalse($filesystem->has($remoteFile->path));

        config(['fileproxy.cache_remote_files' => $beforeValue]);
    }
}
