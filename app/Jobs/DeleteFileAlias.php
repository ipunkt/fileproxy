<?php

namespace App\Jobs;

use App\FileAlias;
use DB;
use Illuminate\Foundation\Bus\Dispatchable;

class DeleteFileAlias
{
    use Dispatchable;

    /**
     * @var FileAlias
     */
    private $fileAlias;

    public function __construct(FileAlias $fileAlias)
    {
        $this->fileAlias = $fileAlias;
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $this->fileAlias->hits()->delete();
            $this->fileAlias->delete();

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
