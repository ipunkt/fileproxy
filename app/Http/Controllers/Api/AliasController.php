<?php

namespace App\Http\Controllers\Api;

use App\FileAlias;
use App\Transformers\FileAliasTransformer;
use Illuminate\Http\JsonResponse;

class AliasController extends ApiController
{
    /**
     * Shows the alias details by an alias id.
     *
     * An alias id is a combined key of proxy file reference and model key of an alias
     *
     * @param string $alias
     * @return JsonResponse
     */
    public function show(string $alias): JsonResponse
    {
        $aliasModel = FileAlias::byCombinedKey($alias);

        return $this->respondItem($aliasModel, new FileAliasTransformer(), 'aliases');
    }
}
