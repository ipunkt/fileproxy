<?php

namespace Tests\Feature;

use App\FileAlias;
use App\Jobs\DeleteFileAlias;
use App\LocalFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class DeleteFileAliasTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_delete_an_file_alias_with_dispatching_job()
    {
        // ARRANGE
        /** @var LocalFile $localFile */
        $localFile = factory(LocalFile::class, 'full')->create();

        $proxyFile = $localFile->proxyFile;

        /** @var FileAlias $fileAlias */
        $fileAlias = factory(FileAlias::class)->create([
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        $this->assertDatabaseHas('file_aliases', [
            'id' => $fileAlias->getKey(),
            'proxy_file_id' => $proxyFile->getKey(),
        ]);

        // ACT
        dispatch(new DeleteFileAlias($fileAlias));

        // ASSERT
        $this->assertDatabaseMissing('file_aliases', [
            'id' => $fileAlias->getKey(),
            'proxy_file_id' => $proxyFile->getKey(),
        ]);
    }

}
