<?php

namespace Tests\Feature\Api;

use App\FileAlias;
use App\LocalFile;
use App\ProxyFile;
use Carbon\Carbon;
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
                    'links' => [
                        'self' => 'http://localhost/api/files/' . $proxyFile->reference,
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
                    'links' => [
                        'self' => 'http://localhost/api/files/' . $proxyFile->reference,
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

    /** @test */
    public function it_can_not_fetch_a_files_index_resource()
    {
        // ARRANGE

        // ACT
        $response = $this->getJson('/api/files');

        // ASSERT
        $response->assertStatus(404);
    }

    /** @test */
    public function it_can_fetch_a_files_resource_by_reference()
    {
        // ARRANGE
        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class, 'full')->create();
        $proxyFile = $localFile->proxyFile;

        // ACT
        $response = $this->getJson('/api/files/' . $proxyFile->reference);

        // ASSERT
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'type' => 'files',
                    'id' => $proxyFile->reference,
                    'attributes' => [
                        'filename' => $proxyFile->filename,
                        'size' => (string)$proxyFile->size,
                        'checksum' => $proxyFile->checksum,
                        'mimetype' => $proxyFile->mimetype,
                        'hits' => 0,
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/files/' . $proxyFile->reference,
                    ],
                ],
            ]);
    }

    /** @test */
    public function it_can_fetch_a_files_resource_by_reference_with_aliases_included()
    {
        // ARRANGE
        $now = Carbon::now();

        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class, 'full')->create();
        $proxyFile = $localFile->proxyFile;
        factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
            'path' => 'test.pdf',
            'valid_from' => $now,
        ]);

        // ACT
        $response = $this->getJson('/api/files/' . $proxyFile->reference . '?include=aliases');

        // ASSERT
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'type' => 'files',
                    'id' => $proxyFile->reference,
                    'attributes' => [
                        'filename' => $proxyFile->filename,
                        'size' => (string)$proxyFile->size,
                        'checksum' => $proxyFile->checksum,
                        'mimetype' => $proxyFile->mimetype,
                        'hits' => 0,
                    ],
                    'relationships' => [
                        'aliases' => [
                            'data' => [
                                [
                                    'type' => 'aliases',
                                    'id' => $proxyFile->reference . '.1',
                                ],
                            ],
                            'links' => [
                                'related' => 'http://localhost/api/files/' . $proxyFile->reference . '/aliases',
                                'self' => 'http://localhost/api/files/' . $proxyFile->reference . '/relationships/aliases',
                            ],
                        ],
                    ],
                    'links' => [
                        'self' => 'http://localhost/api/files/' . $proxyFile->reference,
                    ],
                ],
                'included' => [
                    [
                        'type' => 'aliases',
                        'id' => $proxyFile->reference . '.1',
                        'attributes' => [
                            'path' => 'test.pdf',
                            'valid_from' => $now->toIso8601String(),
                            'valid_until' => null,
                            'hits' => 0,
                            'hits_left' => null,
                            'hits_total' => null,
                        ],
                        'links' => [
                            'self' => 'http://localhost/api/aliases/' . $proxyFile->reference . '.1',
                            'download' => 'http://localhost/test.pdf',
                        ],
                    ]
                ],
            ]);
    }

}
