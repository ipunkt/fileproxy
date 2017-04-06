<?php

namespace App\Repositories;

use App\ProxyFile;

class StatisticsRepository
{
    /**
     * @var ProxyFile
     */
    private $proxyFile;

    public function __construct(ProxyFile $proxyFile)
    {
        $this->proxyFile = $proxyFile;
    }

    /**
     * returns a statistics data collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getStatistics(): \Illuminate\Support\Collection
    {
        $proxyFiles = $this->proxyFile->all();

        $size = $proxyFiles->sum('size');

        $aliases = $proxyFiles->sum(function (ProxyFile $proxyFile) {
            return $proxyFile->aliases()->count();
        });

        $hits = $proxyFiles->sum(function (ProxyFile $proxyFile) {
            return $proxyFile->hits()->count();
        });

        return collect([
            'size' => $size,
            'files' => $proxyFiles->count(),
            'aliases' => $aliases,
            'hits' => $hits,
        ]);
    }
}
