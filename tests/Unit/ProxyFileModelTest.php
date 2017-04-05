<?php

namespace Tests\Unit;

use App\AliasHit;
use App\FileAlias;
use App\LocalFile;
use App\ProxyFile;
use App\RemoteFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ProxyFileModelTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_a_local_proxy_file_model()
    {
        // ARRANGE

    	// ACT
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create();

    	// ASSERT
        $this->assertTrue($proxyFile->save());
        $this->assertEquals('local', $proxyFile->type);
    }

    /** @test */
    public function it_can_create_a_remote_proxy_file_model()
    {
        // ARRANGE

    	// ACT
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'remote')->create();

    	// ASSERT
        $this->assertTrue($proxyFile->save());
        $this->assertEquals('remote', $proxyFile->type);
    }

    /** @test */
    public function it_can_resolve_local_file_instance()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create();

        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

    	// ACT
    	$localFileResolved = $proxyFile->localFile;

    	// ASSERT
        $this->assertInstanceOf(LocalFile::class, $localFileResolved);
    	$this->assertSame($localFile->getKey(), $localFileResolved->getKey());
    }


    /** @test */
    public function it_can_resolve_remote_file_instance()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'remote')->create();

        /** @var RemoteFile $remoteFile */
        $remoteFile = factory(RemoteFile::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

    	// ACT
    	$remoteFileResolved = $proxyFile->remoteFile;

    	// ASSERT
        $this->assertInstanceOf(RemoteFile::class, $remoteFileResolved);
    	$this->assertSame($remoteFile->getKey(), $remoteFileResolved->getKey());
    }

    /** @test */
    public function it_can_resolve_a_local_file_instance_depending_on_the_type()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create();

        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

    	// ACT
    	$localFileResolved = $proxyFile->file;

    	// ASSERT
        $this->assertInstanceOf(LocalFile::class, $localFileResolved);
    	$this->assertSame($localFile->getKey(), $localFileResolved->getKey());
    }

    /** @test */
    public function it_can_resolve_a_remote_file_instance_depending_on_the_type()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'remote')->create();

        /** @var RemoteFile $remoteFile */
        $remoteFile = factory(RemoteFile::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

    	// ACT
    	$remoteFileResolved = $proxyFile->file;

    	// ASSERT
        $this->assertInstanceOf(RemoteFile::class, $remoteFileResolved);
    	$this->assertSame($remoteFile->getKey(), $remoteFileResolved->getKey());
    }

    /** @test */
    public function it_can_resolve_all_hits_for_a_proxy_file_instance()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create();

        /** @var FileAlias $fileAlias1 */
        $fileAlias1 = $proxyFile->aliases()->create(factory(FileAlias::class)->make()->toArray());

        /** @var AliasHit $aliasHit1 */
        $fileAlias1->hits()->create(factory(AliasHit::class)->make()->toArray());
        $fileAlias1->hits()->create(factory(AliasHit::class)->make()->toArray());

        /** @var FileAlias $fileAlias2 */
        $fileAlias2 = $proxyFile->aliases()->create(factory(FileAlias::class)->make()->toArray());

        $fileAlias2->hits()->create(factory(AliasHit::class)->make()->toArray());
        $fileAlias2->hits()->create(factory(AliasHit::class)->make()->toArray());
        $fileAlias2->hits()->create(factory(AliasHit::class)->make()->toArray());

    	// ACT
        $hits = $proxyFile->hits;

    	// ASSERT
        $this->assertCount(5, $hits);
        $this->assertInstanceOf(AliasHit::class, $hits[0]);
        $this->assertEquals(5, $proxyFile->hits()->count());
    }
}
