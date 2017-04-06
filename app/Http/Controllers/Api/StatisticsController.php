<?php

namespace App\Http\Controllers\Api;

use App\Repositories\StatisticsRepository;
use App\Transformers\StatisticsTransformer;

class StatisticsController extends ApiController
{
    /**
     * sends statistics data
     *
     * @param StatisticsRepository $statisticsRepository
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(StatisticsRepository $statisticsRepository)
    {
        return $this->respondItem(
            $statisticsRepository->getStatistics(),
            new StatisticsTransformer(),
            'statistics'
        );
    }
}
