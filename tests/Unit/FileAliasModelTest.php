<?php

namespace Tests\Unit;

use App\AliasHit;
use App\FileAlias;
use App\ProxyFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FileAliasModelTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_a_file_alias_model()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        // ACT
        /** @var FileAlias $fileAlias */
        $fileAlias = factory(FileAlias::class)->make([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        // ASSERT
        $this->assertTrue($fileAlias->save());
    }

    /** @test */
    public function it_can_resolve_all_file_alias_models_by_proxy_file()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        /** @var FileAlias $fileAlias */
        $fileAlias1 = factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);
        $fileAlias2 = factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        // ACT
        $fileAliases = $proxyFile->aliases;

        // ASSERT
        $this->assertCount(2, $fileAliases);
        $this->assertInstanceOf(FileAlias::class, $fileAliases[0]);
        $this->assertEquals($fileAlias1->getKey(), $fileAliases[0]->getKey());
        $this->assertInstanceOf(FileAlias::class, $fileAliases[1]);
        $this->assertEquals($fileAlias2->getKey(), $fileAliases[1]->getKey());
    }

    /** @test */
    public function it_can_resolve_proxy_file_from_file_alias_instance()
    {
        // ARRANGE
        /** @var FileAlias $fileAlias */
        $fileAlias = factory(FileAlias::class, 'full')->create();

        // ACT
        $proxyFile = $fileAlias->proxyFile;

        // ASSERT
        $this->assertInstanceOf(ProxyFile::class, $proxyFile);
        $this->assertEquals(1, $proxyFile->getKey());
    }

    /** @test */
    public function it_can_resolve_all_file_hits_from_file_alias_instance()
    {
        // ARRANGE
        /** @var FileAlias $fileAlias */
        $fileAlias = factory(FileAlias::class, 'full')->create();

        $aliasHit1 = factory(AliasHit::class)->create([
            'file_alias_id' => $fileAlias->getKey(),
        ]);
        $aliasHit2 = factory(AliasHit::class)->create([
            'file_alias_id' => $fileAlias->getKey(),
        ]);

        // ACT
        $aliasHits = $fileAlias->hits;

        // ASSERT
        $this->assertCount(2, $aliasHits);
        $this->assertInstanceOf(AliasHit::class, $aliasHits[0]);
        $this->assertEquals($aliasHit1->getKey(), $aliasHits[0]->getKey());
        $this->assertInstanceOf(AliasHit::class, $aliasHits[1]);
        $this->assertEquals($aliasHit2->getKey(), $aliasHits[1]->getKey());
    }
}
