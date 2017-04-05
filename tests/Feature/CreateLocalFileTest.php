<?php

namespace Tests\Feature;

use App\Jobs\CreateLocalFile;
use App\LocalFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class CreateLocalFileTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_create_a_local_file_by_using_uploaded_file()
    {
        // ARRANGE
        $filesystem = $this->mockedVirtualFilesystem();
        $reference = Uuid::uuid4();

        $file = UploadedFile::fake();

        // ACT
        dispatch(new CreateLocalFile($reference, $file->create('test.doc', 1)));

        // ASSERT
        $this->assertDatabaseHas('proxy_files', [
            'reference' => $reference,
            'type' => 'local',
            'filename' => 'test.doc',
            'mimetype' => 'application/msword',
            'size' => 1024,
            'checksum' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
        ]);
        $this->assertDatabaseHas('local_files', [
            //  sha1(1) => 356a192b7913b04c54574d18c28d46e6395428ab
            'path' => 'local/35/6a/356a192b7913b04c54574d18c28d46e6395428ab',
        ]);
        $localFile = LocalFile::first();
        $this->assertTrue($filesystem->has($localFile->path));
        $this->assertEquals('', $filesystem->read($localFile->path));
    }
}
