<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;
use Symfony\Component\HttpFoundation\Response;

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
     * responds collection as json api response.
     *
     * @param $collection
     * @param TransformerAbstract $transformer
     * @param string $resourceName
     * @return JsonResponse
     */
    protected function respondCollection($collection, TransformerAbstract $transformer, string $resourceName): JsonResponse
    {
        $resource = new Collection($collection, $transformer, $resourceName);
        $data = $this->fractal->createData($resource)->toArray();

        return $this->respondData($data, Response::HTTP_OK);
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

        return $this->respondData($data, Response::HTTP_OK);
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

        return $this->respondData($data, Response::HTTP_CREATED);
    }

    protected function respondNoContent(): JsonResponse
    {
        return $this->respondData(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * responds data as json response.
     *
     * @param array|null $data
     * @param int $statusCode
     * @return JsonResponse
     */
    protected function respondData(array $data = null, $statusCode = Response::HTTP_OK): JsonResponse
    {
        return response()->json($data, $statusCode)
            ->header('Content-Type', 'application/vnd.api+json');
    }
}
