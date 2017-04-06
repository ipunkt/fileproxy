<?php

namespace Tests;

trait JsonApiRequestModelConcern
{
    /**
     * creates a request model
     *
     * @param string $type
     * @param array $data
     * @param null $id
     * @return array
     */
    protected function createRequestModel(string $type, array $data = [], $id = null): array
    {
        return [
            'data' => [
                'id' => $id,
                'type' => $type,
                'attributes' => $data,
            ]
        ];
    }
}