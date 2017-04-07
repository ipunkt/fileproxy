<?php

namespace App\Transformers;

use App\ProxyFile;
use League\Fractal\TransformerAbstract;

class ProxyFileTransformer extends TransformerAbstract
{
    /**
     * Resources that can be included if requested.
     *
     * @var array
     */
    protected $availableIncludes = [
        'aliases'
    ];

    /**
     * transforms a proxy file.
     *
     * @param ProxyFile $proxyFile
     * @return array
     */
    public function transform(ProxyFile $proxyFile): array
    {
        return [
            'id' => $proxyFile->reference,
            'filename' => $proxyFile->filename,
            'size' => $proxyFile->size,
            'checksum' => $proxyFile->checksum,
            'mimetype' => $proxyFile->mimetype,
            'hits' => $proxyFile->hits()->count(),
        ];
    }

    /**
     * includes aliases.
     *
     * @param ProxyFile $proxyFile
     * @return \League\Fractal\Resource\Collection
     */
    public function includeAliases(ProxyFile $proxyFile)
    {
        $aliases = $proxyFile->aliases;

        return $this->collection($aliases, new FileAliasTransformer(), 'aliases');
    }
}
