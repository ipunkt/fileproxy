<?php

namespace App\Transformers;

use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class StatisticsTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param Collection $data
     * @return array
     */
    public function transform($data)
    {
        return ['id' => null] + $data->toArray();
    }
}
