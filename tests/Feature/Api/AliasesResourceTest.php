<?php

namespace Tests\Feature\Api;

use App\FileAlias;
use App\ProxyFile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\JsonApiRequestModelConcern;
use Tests\TestCase;

class AliasesResourceTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions, JsonApiRequestModelConcern;

    /** @test */
    public function it_can_store_a_new_alias_by_api()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        /** @var FileAlias $alias */
        $alias = factory(FileAlias::class, 'request')->make();

        // ACT
        $response = $this->postJson(
            '/api/files/' . $proxyFile->reference . '/aliases',
            $this->createRequestModel('aliases', $alias->toArray())
        );

        // ASSERT
        $response->assertStatus(200);
        $data = $response->json();
        $response->assertExactJson([
                'data' => [
                    'type' => 'aliases',
                    'id' => $proxyFile->reference . '.1',
                    'attributes' => [
                        'path' => $alias->path,
                        'valid_from' => $alias->valid_from->toIso8601String(),
                        'valid_until' => $alias->valid_until === null ? null : $alias->valid_until->toIso8601String(),
                        'hits' => array_get($data, 'data.attributes.hits'),
                        'hits_left' => array_get($data, 'data.attributes.hits_left'),
                        'hits_total' => array_get($data, 'data.attributes.hits_total'),
                    ],
                    'links' => [
                        'download' => 'http://localhost/' . $alias->path,
                        'self' => 'http://localhost/api/aliases/' . $proxyFile->reference . '.1'
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_retrieve_an_alias_resource_by_api()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        /** @var FileAlias $alias */
        $alias = factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->id,
        ]);

        // ACT
        $response = $this->getJson('/api/aliases/' . $proxyFile->reference . '.' . $alias->getKey());

        // ASSERT
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    'type' => 'aliases',
                    'id' => $proxyFile->reference . '.' . $alias->getKey(),
                    'attributes' => [
                        'path' => $alias->path,
                        'valid_from' => $alias->valid_from->toIso8601String(),
                        'valid_until' => null,
                        'hits' => 0,
                        'hits_left' => null,
                        'hits_total' => null
                    ],
                    'links' => [
                        'download' => 'http://localhost/' . $alias->path,
                        'self' => 'http://localhost/api/aliases/' . $proxyFile->reference . '.' . $alias->getKey()
                    ]
                ]
            ]);
    }

    /** @test */
    public function it_can_delete_an_alias_via_api()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        /** @var FileAlias $alias */
        $alias = factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->id,
        ]);

        // ACT
        $response = $this->deleteJson('/api/aliases/' . $proxyFile->reference . '.' . $alias->getKey());

        // ASSERT
        $response->assertStatus(204);
    }

    /** @test */
    public function it_can_retrieve_all_aliases_by_api_through_relationship_url()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        /** @var FileAlias $alias */
        $alias = factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->id,
        ]);

        // ACT
        $response = $this->getJson('/api/files/' . $proxyFile->reference . '/relationships/aliases');

        // ASSERT
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'type' => 'aliases',
                        'id' => $proxyFile->reference . '.1',
                        'attributes' => [
                            'path' => $alias->path,
                            'valid_from' => $alias->valid_from->toIso8601String(),
                            'valid_until' => null,
                            'hits' => 0,
                            'hits_left' => null,
                            'hits_total' => null
                        ],
                        'links' => [
                            'download' => 'http://localhost/' . $alias->path,
                            'self' => 'http://localhost/api/aliases/' . $proxyFile->reference . '.1'
                        ]
                    ]
                ]

            ]);
    }

    /** @test */
    public function it_can_retrieve_all_aliases_by_api_through_relationship_short_url()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        /** @var FileAlias $alias */
        $alias = factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->id,
        ]);

        // ACT
        $response = $this->getJson('/api/files/' . $proxyFile->reference . '/aliases');

        // ASSERT
        $response->assertStatus(200)
            ->assertExactJson([
                'data' => [
                    [
                        'type' => 'aliases',
                        'id' => $proxyFile->reference . '.1',
                        'attributes' => [
                            'path' => $alias->path,
                            'valid_from' => $alias->valid_from->toIso8601String(),
                            'valid_until' => null,
                            'hits' => 0,
                            'hits_left' => null,
                            'hits_total' => null
                        ],
                        'links' => [
                            'download' => 'http://localhost/' . $alias->path,
                            'self' => 'http://localhost/api/aliases/' . $proxyFile->reference . '.1'
                        ]
                    ]
                ]

            ]);
    }

}
