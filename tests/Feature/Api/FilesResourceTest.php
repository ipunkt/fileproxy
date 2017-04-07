<?php

namespace Tests\Feature\Api;

use App\ProxyFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\JsonApiRequestModelConcern;
use Tests\TestCase;

class FilesResourceTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, JsonApiRequestModelConcern;

    /** @test */
    public function it_can_create_an_attached_file_upload_as_proxy_file()
    {
        // ARRANGE
        config(['filesystems.default' => 'local']);

        // ACT
        $response = $this->postJson('/api/files', $this->createRequestModel('files', [
            'type' => 'attachment',
            'source' => base64_encode('test'),
            'filename' => 'test.txt',
        ]));

        $proxyFile = ProxyFile::first();

        // ASSERT
        $response->assertStatus(201)
            ->assertExactJson([
                'data' => [
                    'type' => 'files',
                    'id' => $proxyFile->reference,
                    'attributes' => [
                        'filename' => 'test.txt',
                        'size' => '4',
                        'checksum' => 'a94a8fe5ccb19ba61c4c0873d391e987982fbbd3',
                        'mimetype' => 'text/plain',
                        'hits' => 0,
                    ],
                ]
            ]);

        $this->assertDatabaseHas('proxy_files', [
            'id' => 1,
            'filename' => 'test.txt',
        ]);
        $this->assertDatabaseHas('local_files', [
            'proxy_file_id' => 1,
        ]);

        //  sha1(1) => 356a192b7913b04c54574d18c28d46e6395428ab
        $this->assertFileExists(storage_path('app/local/35/6a/356a192b7913b04c54574d18c28d46e6395428ab'));
        unlink(storage_path('app/local/35/6a/356a192b7913b04c54574d18c28d46e6395428ab'));
        rmdir(storage_path('app/local/35/6a'));
        rmdir(storage_path('app/local/35'));
    }

    /** @test */
    public function it_can_create_an_url_as_proxy_file()
    {
        // ARRANGE
        config(['filesystems.default' => 'local']);
        config(['fileproxy.cache_remote_files' => true]);

        // ACT
        $response = $this->postJson('/api/files', $this->createRequestModel('files', [
            'type' => 'uri',
            'source' => 'https://cdn.polyfill.io/v2/polyfill.min.js',
        ]));

        $proxyFile = ProxyFile::first();

        // ASSERT
        $response->assertStatus(201)
            ->assertExactJson([
                'data' => [
                    'type' => 'files',
                    'id' => $proxyFile->reference,
                    'attributes' => [
                        'filename' => 'polyfill.min.js',
                        'size' => '72',
                        'checksum' => 'e3d8fc7eec3e4338218b844d40a1ae86cc8581c6',
                        'mimetype' => 'application/javascript',
                        'hits' => 0,
                    ],
                ]
            ]);

        $this->assertDatabaseHas('proxy_files', [
            'id' => 1,
            'filename' => 'polyfill.min.js',
        ]);
        $this->assertDatabaseHas('remote_files', [
            'proxy_file_id' => 1,
        ]);

        //  sha1(1) => 356a192b7913b04c54574d18c28d46e6395428ab
        $this->assertFileExists(storage_path('app/remote/35/6a/356a192b7913b04c54574d18c28d46e6395428ab'));
        unlink(storage_path('app/remote/35/6a/356a192b7913b04c54574d18c28d46e6395428ab'));
        rmdir(storage_path('app/remote/35/6a'));
        rmdir(storage_path('app/remote/35'));
    }

}
