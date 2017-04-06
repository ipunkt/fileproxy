<?php

namespace App\Transformers;

use App\ProxyFile;
use League\Fractal\TransformerAbstract;

class ProxyFileTransformer extends TransformerAbstract
{
    /**
     * transforms a proxy file
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
        ];
    }
}