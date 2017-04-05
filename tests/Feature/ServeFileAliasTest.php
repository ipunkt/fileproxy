<?php

namespace Tests\Feature;

use App\Exceptions\FileAliasCanNotBeServed;
use App\FileAlias;
use App\Jobs\CreateFileAlias;
use App\Jobs\ServeFileAlias;
use App\LocalFile;
use App\ProxyFile;
use App\ServableFile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class ServeFileAliasTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_serve_an_unlimited_file_alias()
    {
        // ARRANGE
        $filesystem = $this->mockedVirtualFilesystem();

        $reference = Uuid::uuid4();
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create([
            'reference' => $reference->toString(),
            'filename' => 'rezept-2017-01.pdf',
            'size' => 4,
            'mimetype' => 'application/pdf',
        ]);
        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        $filesystem->put($localFile->getLocalStoragePath(), 'test');
        $localFile->path = $localFile->getLocalStoragePath();
        $localFile->save();

        $hits = null;
        $validFrom = Carbon::now();
        $validUntil = null;
        $path = 'rezept-21.pdf';

        (new CreateFileAlias($proxyFile, $path, $hits, $validFrom, $validUntil))->handle();

        // ACT
        $response = dispatch(new ServeFileAlias($path));

        // ASSERT
        $this->assertInstanceOf(ServableFile::class, $response);
        $this->asserttrue($filesystem->has($localFile->getLocalStoragePath()));

        $this->assertDatabaseHas('alias_hits', [
            'file_alias_id' => FileAlias::first()->getKey(),
        ]);
    }

    /** @test */
    public function it_can_serve_an_limited_file_alias_once()
    {
        // ARRANGE
        $filesystem = $this->mockedVirtualFilesystem();

        $reference = Uuid::uuid4();
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create([
            'reference' => $reference->toString(),
            'filename' => 'rezept-2017-01.pdf',
            'size' => 4,
            'mimetype' => 'application/pdf',
        ]);
        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        $filesystem->put($localFile->getLocalStoragePath(), 'test');
        $localFile->path = $localFile->getLocalStoragePath();
        $localFile->save();

        $hits = 1;
        $validFrom = Carbon::now();
        $validUntil = null;
        $path = 'rezept-21.pdf';

        (new CreateFileAlias($proxyFile, $path, $hits, $validFrom, $validUntil))->handle();

        // ACT
        $response = dispatch(new ServeFileAlias($path));

        // ASSERT
        $this->assertInstanceOf(ServableFile::class, $response);
        $this->asserttrue($filesystem->has($localFile->getLocalStoragePath()));

        $this->assertDatabaseHas('file_aliases', [
            'proxy_file_id' => $proxyFile->getKey(),
            'hits_left' => 0,
        ]);
        $this->assertDatabaseHas('alias_hits', [
            'file_alias_id' => FileAlias::first()->getKey(),
        ]);

        $this->expectException(FileAliasCanNotBeServed::class);
        $response = dispatch(new ServeFileAlias($path));
    }

    /** @test */
    public function it_can_not_serve_an_time_limited_file_alias_outside_a_defined_period()
    {
        // ARRANGE
        $filesystem = $this->mockedVirtualFilesystem();

        $reference = Uuid::uuid4();
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create([
            'reference' => $reference->toString(),
            'filename' => 'rezept-2017-01.pdf',
            'size' => 4,
            'mimetype' => 'application/pdf',
        ]);
        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        $filesystem->put($localFile->getLocalStoragePath(), 'test');
        $localFile->path = $localFile->getLocalStoragePath();
        $localFile->save();

        $hits = 1;
        $validFrom = Carbon::tomorrow();
        $validUntil = Carbon::tomorrow()->addDay();
        $path = 'rezept-21.pdf';

        (new CreateFileAlias($proxyFile, $path, $hits, $validFrom, $validUntil))->handle();

        // ACT
        $this->expectException(FileAliasCanNotBeServed::class);
        $response = dispatch(new ServeFileAlias($path));
    }
}
