<?php

namespace App\Http\Controllers;

use App\Repositories\StatisticsRepository;

class StatisticsController extends Controller
{
    /**
     * sends statistics data.
     *
     * @param StatisticsRepository $statisticsRepository
     * @return $this
     */
    public function index(StatisticsRepository $statisticsRepository)
    {
        return view('statistics')
            ->with($statisticsRepository->getStatistics()->all());
    }
}
