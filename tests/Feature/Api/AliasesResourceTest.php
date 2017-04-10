<?php

namespace Tests\Feature\Api;

use App\FileAlias;
use App\ProxyFile;
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
        $response->assertStatus(204);
    }

}
