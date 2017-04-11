<?php

namespace App\Transformers;

use App\FileAlias;
use League\Fractal\TransformerAbstract;

class FileAliasTransformer extends TransformerAbstract
{
    public function transform(FileAlias $model): array
    {
        return [
            'id' => $model->proxyFile->reference . '.' . $model->getKey(),
            'path' => $model->path,
            'valid_from' => $model->valid_from->toIso8601String(),
            'valid_until' => $model->valid_until === null ? null : $model->valid_until->toIso8601String(),
            'hits' => $model->hits()->count(),
            'hits_left' => $model->hits_left,
            'hits_total' => $model->hits_total >= 0 ? $model->hits_total : null,

            'links' => [
                'download' => route('serve', ['alias' => $model->path]),
            ],
        ];
    }
}
