<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ApiController extends Controller
{
    /**
     * @var Manager
     */
    private $fractal;

    public function __construct(Manager $fractal)
    {
        $this->fractal = $fractal;
    }

    /**
     * responds item as json api response.
     *
     * @param $item
     * @param TransformerAbstract $transformer
     * @param string $resourceName
     * @return JsonResponse
     */
    protected function respondItem($item, TransformerAbstract $transformer, string $resourceName): JsonResponse
    {
        $resource = new Item($item, $transformer, $resourceName);
        $data = $this->fractal->createData($resource)->toArray();

        return response()->json($data)
            ->header('Content-Type', 'application/vnd.api+json');
    }

    /**
     * responds created resource.
     *
     * @param $item
     * @param TransformerAbstract $transformer
     * @param string $resourceName
     * @return JsonResponse
     */
    protected function respondCreated($item, TransformerAbstract $transformer, string $resourceName): JsonResponse
    {
        $resource = new Item($item, $transformer, $resourceName);
        $data = $this->fractal->createData($resource)->toArray();

        return response()->json($data, 201)
            ->header('Content-Type', 'application/vnd.api+json');
    }
}
