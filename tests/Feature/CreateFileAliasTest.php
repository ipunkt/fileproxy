<?php

namespace Tests\Feature;

use App\Jobs\CreateFileAlias;
use App\ProxyFile;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CreateFileAliasTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_a_file_alias_for_unlimited_access()
    {
        // ARRANGE
        $reference = Uuid::uuid4();
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create([
            'reference' => $reference->toString(),
        ]);
        $hits = null;
        $validFrom = Carbon::now();
        $validUntil = null;
        $path = 'rezept-21.pdf';

    	// ACT
        dispatch(new CreateFileAlias($proxyFile, $path, $hits, $validFrom, $validUntil));

    	// ASSERT
        $this->assertDatabaseHas('file_aliases', [
            'path' => 'rezept-21.pdf',
            'hits_left' => null,
            'valid_from' => $validFrom->toDateTimeString(),
            'valid_until' => null,
        ]);
    }

    /** @test */
    public function it_can_create_a_file_alias_for_hit_limited_access()
    {
        // ARRANGE
        $reference = Uuid::uuid4();
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create([
            'reference' => $reference->toString(),
        ]);
        $hits = 1;
        $validFrom = Carbon::now();
        $validUntil = null;
        $path = 'rezept-21.pdf';

    	// ACT
        dispatch(new CreateFileAlias($proxyFile, $path, $hits, $validFrom, $validUntil));

    	// ASSERT
        $this->assertDatabaseHas('file_aliases', [
            'path' => 'rezept-21.pdf',
            'hits_left' => 1,
            'valid_from' => $validFrom->toDateTimeString(),
            'valid_until' => null,
        ]);
    }

    /** @test */
    public function it_can_create_a_file_alias_for_hit_and_time_limited_access()
    {
        // ARRANGE
        $reference = Uuid::uuid4();
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create([
            'reference' => $reference->toString(),
        ]);
        $hits = 1;
        $validFrom = Carbon::now();
        $validUntil = Carbon::tomorrow();
        $path = 'rezept-21.pdf';

    	// ACT
        dispatch(new CreateFileAlias($proxyFile, $path, $hits, $validFrom, $validUntil));

    	// ASSERT
        $this->assertDatabaseHas('file_aliases', [
            'path' => 'rezept-21.pdf',
            'hits_left' => 1,
            'valid_from' => $validFrom->toDateTimeString(),
            'valid_until' => $validUntil->toDateTimeString(),
        ]);
    }

    /** @test */
    public function it_can_create_a_file_alias_for_time_limited_access()
    {
        // ARRANGE
        // ARRANGE
        $reference = Uuid::uuid4();
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class, 'local')->create([
            'reference' => $reference->toString(),
        ]);
        $hits = null;
        $validFrom = Carbon::now();
        $validUntil = Carbon::tomorrow();
        $path = 'rezept-21.pdf';

    	// ACT
        dispatch(new CreateFileAlias($proxyFile, $path, $hits, $validFrom, $validUntil));

    	// ASSERT
        $this->assertDatabaseHas('file_aliases', [
            'path' => 'rezept-21.pdf',
            'hits_left' => null,
            'valid_from' => $validFrom->toDateTimeString(),
            'valid_until' => $validUntil->toDateTimeString(),
        ]);
    }
}
