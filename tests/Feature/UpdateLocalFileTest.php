<?php

namespace Tests\Feature;

use App\Jobs\UpdateLocalFile;
use App\LocalFile;
use App\ProxyFile;
use Illuminate\Http\Testing\FileFactory;
use League\Flysystem\Filesystem;
use Tests\TestCase;
use Ramsey\Uuid\Uuid;
use App\Jobs\CreateLocalFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UpdateLocalFileTest extends TestCase
{
    use DatabaseMigrations, DatabaseTransactions;

    /** @test */
    public function it_can_update_a_previous_uploaded_file_content()
    {
        // ARRANGE
        $reference = Uuid::uuid4();
        $filesystem = $this->mockedVirtualFilesystem();
        $file = UploadedFile::fake();
        $proxyFile = $this->prepareExistingUploadedFile($reference, $filesystem, $file);

    	// ACT
        $updateLocalFile = new UpdateLocalFile($proxyFile, $file->create('test2.doc', 2));
        $updateLocalFile->handle();

    	// ASSERT
    	$this->assertDatabaseHas('proxy_files', [
    	    'id' => $proxyFile->id,
            'reference' => $proxyFile->reference,
            'type' => 'local',
            'filename' => 'test2.doc',
            'mimetype' => 'application/msword',
            'size' => 2048,
            'checksum' => 'da39a3ee5e6b4b0d3255bfef95601890afd80709',
        ]);
        $this->assertDatabaseHas('local_files', [
            //  sha1(2) => da4b9237bacccdf19c0760cab7aec4a8359010b0
            'path' => 'local/da/4b/da4b9237bacccdf19c0760cab7aec4a8359010b0',
        ]);
        $this->assertDatabaseMissing('local_files', [
            'path' => 'local/35/6a/356a192b7913b04c54574d18c28d46e6395428ab',
        ]);

        $localFile = LocalFile::first();
        $this->assertFalse($filesystem->has('local/35/6a/356a192b7913b04c54574d18c28d46e6395428ab'));
        $this->assertTrue($filesystem->has($localFile->path));
        $this->assertEquals('', $filesystem->read($localFile->path));
    }

    private function prepareExistingUploadedFile(string $reference, Filesystem $filesystem, FileFactory $file): ProxyFile
    {
        // ARRANGE

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

        return ProxyFile::byReference($reference);
    }
}
