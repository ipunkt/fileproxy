<?php

namespace Tests\Unit;

use App\LocalFile;
use App\ProxyFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LocalFileModelTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_a_local_file_model()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        // ACT
        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class)->make([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        // ASSERT
        $this->assertTrue($localFile->save());
    }

    /** @test */
    public function it_can_retrieve_proxy_file_from_local_file_instance()
    {
        // ARRANGE
        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class, 'full')->create();

        // ACT
        $proxyFile = $localFile->proxyFile;

        // ASSERT
        $this->assertEquals(1, $proxyFile->getKey());
        $this->assertInstanceOf(ProxyFile::class, $proxyFile);
    }
}
