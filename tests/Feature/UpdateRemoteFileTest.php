<?php

namespace Tests\Feature;

use App\Jobs\UpdateRemoteFile;
use App\ProxyFile;
use App\RemoteFile;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use App\Jobs\CreateRemoteFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateRemoteFileTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_update_an_existing_remote_proxyfile()
    {
        // ARRANGE
        $filesystem = $this->mockedVirtualFilesystem();
        $proxyFile = $this->prepareRemoteProxyFile();

    	// ACT
        dispatch(new UpdateRemoteFile($proxyFile, 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/core.js'));

    	// ASSERT
        $this->assertDatabaseMissing('proxy_files', [
            'id' => $proxyFile->id,
            'reference' => $proxyFile->reference,
            'type' => 'remote',
            'filename' => 'polyfill.min.js',
            'mimetype' => 'application/javascript',
        ]);
        $this->assertDatabaseHas('proxy_files', [
            'id' => $proxyFile->id,
            'reference' => $proxyFile->reference,
            'type' => 'remote',
            'filename' => 'core.js',
            'mimetype' => 'application/javascript',
        ]);

        $this->assertDatabaseMissing('remote_files', [
            'url' => 'https://cdn.polyfill.io/v2/polyfill.min.js',
            'options' => null,
            'proxy_file_id' => $proxyFile->id,
            //  sha1(1) => 356a192b7913b04c54574d18c28d46e6395428ab
            'path' => 'remote/35/6a/356a192b7913b04c54574d18c28d46e6395428ab',
//            'path' => null,
        ]);
        $this->assertDatabaseHas('remote_files', [
            'url' => 'https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/core.js',
            'options' => null,
            'proxy_file_id' => $proxyFile->id,
            //  //  sha1(2) => da4b9237bacccdf19c0760cab7aec4a8359010b0
            'path' => 'remote/da/4b/da4b9237bacccdf19c0760cab7aec4a8359010b0',
        ]);
        $this->assertFalse($filesystem->has('remote/35/6a/356a192b7913b04c54574d18c28d46e6395428ab'));
    }

    private function prepareRemoteProxyFile(): ProxyFile
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

        return ProxyFile::byReference($reference);
    }
}
