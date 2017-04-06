<?php

namespace Tests\Feature;

use App\Jobs\UpdateProxyFile;
use App\ProxyFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class UpdateProxyFileTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_update_the_proxy_file_data()
    {
        // ARRANGE
        /** @var ProxyFile $proxyFile */
        $proxyFile = factory(ProxyFile::class)->create([
            'filename' => 'original.pdf',
        ]);

        // ACT
        dispatch(new UpdateProxyFile($proxyFile, 'new.pdf'));

        // ASSERT
        $this->assertDatabaseHas('proxy_files', [
            'id' => $proxyFile->getKey(),
            'filename' => 'new.pdf',
        ]);
    }

}
