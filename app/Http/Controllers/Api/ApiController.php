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
        return $this->respondData($data, 200);
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
        return $this->respondData($data, 201);
    }

    /**
     * responds data as json response.
     *
     * @param array $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function respondData(array $data, $statusCode = 200): JsonResponse
    {
        return response()->json($data, $statusCode)
            ->header('Content-Type', 'application/vnd.api+json');
    }
}
