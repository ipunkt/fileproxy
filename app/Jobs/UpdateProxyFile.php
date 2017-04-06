<?php

namespace App\Jobs;

use DB;
use App\ProxyFile;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateProxyFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    /**
     * @var ProxyFile
     */
    private $proxyFile;

    /**
     * @var string
     */
    private $filename;

    public function __construct(ProxyFile $proxyFile, string $filename)
    {
        $this->proxyFile = $proxyFile;
        $this->filename = $filename;
    }

    public function handle()
    {
        try {
            DB::beginTransaction();

            $this->proxyFile->update([
                'filename' => $this->filename,
            ]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
