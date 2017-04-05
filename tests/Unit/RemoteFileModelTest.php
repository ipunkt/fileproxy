<?php

namespace Tests\Unit;

use App\ProxyFile;
use App\RemoteFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RemoteFileModelTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_a_remote_file_model()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

    	// ACT
        /** @var RemoteFile $remoteFile */
        $remoteFile = factory(RemoteFile::class)->make([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

    	// ASSERT
    	$this->assertTrue($remoteFile->save());
    }

    /** @test */
    public function it_can_retrieve_proxy_file_from_remote_file_instance()
    {
        // ARRANGE
        /** @var RemoteFile $remoteFile */
        $remoteFile = factory(RemoteFile::class, 'full')->create();

    	// ACT
        $proxyFile = $remoteFile->proxyFile;

    	// ASSERT
    	$this->assertEquals(1, $proxyFile->getKey());
    	$this->assertInstanceOf(ProxyFile::class, $proxyFile);
    }
}
