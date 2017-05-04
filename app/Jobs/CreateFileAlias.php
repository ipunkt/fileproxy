<?php

namespace App\Jobs;

use App\ProxyFile;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateFileAlias
{
    use Dispatchable;

    /**
     * @var \App\ProxyFile
     */
    private $proxyFile;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $hits;

    /**
     * @var \Carbon\Carbon
     */
    private $validFrom;

    /**
     * @var \Carbon\Carbon
     */
    private $validUntil;

    public function __construct(
        ProxyFile $proxyFile,
        string $path,
        int $hits = null,
        Carbon $validFrom = null,
        Carbon $validUntil = null
    ) {
        $this->proxyFile = $proxyFile;
        $this->path = ltrim($path, '/');
        $this->hits = $hits;
        $this->validFrom = $validFrom ?? Carbon::now();
        $this->validUntil = $validUntil;
    }

    public function handle()
    {
        try {
            \DB::beginTransaction();

            $alias = $this->proxyFile->aliases()->create([
                'path' => $this->path,
                'hits_left' => $this->hits,
                'valid_from' => $this->validFrom,
                'valid_until' => $this->validUntil,
            ]);

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            throw $exception;
        }

        return $alias ?? null;
    }
}
