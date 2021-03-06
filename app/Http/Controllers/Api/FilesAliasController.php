<?php

namespace App\Http\Controllers\Api;

use App\FileAlias;
use App\Http\Requests\Api\CreateAliasRequest;
use App\Jobs\CreateFileAlias;
use App\ProxyFile;
use App\Transformers\FileAliasTransformer;
use Illuminate\Http\JsonResponse;

class FilesAliasController extends ApiController
{
    /**
     * stores a new alias for given proxy file.
     *
     * @param CreateAliasRequest $request
     * @param string $file
     * @return JsonResponse
     */
    public function store(CreateAliasRequest $request, string $file): JsonResponse
    {
        $this->validate($request, [
            'data.attributes.path' => 'route_url:' . $request->get('path') . ',serve',
        ]);

        $proxyFile = ProxyFile::byReference($file);

        /** @var FileAlias $fileAlias */
        $fileAlias = $this->dispatch(new CreateFileAlias($proxyFile, $request->path(), $request->hits(), $request->validFrom(), $request->validUntil()));

        return $this->respondItem($fileAlias, new FileAliasTransformer(), 'aliases');
    }
}
