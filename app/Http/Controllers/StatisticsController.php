<?php

namespace App\Http\Controllers;

use App\ProxyFile;

class StatisticsController extends Controller
{
    public function index()
    {
        $proxyFiles = ProxyFile::all();

        $size = $proxyFiles->sum('size');

        $aliases = $proxyFiles->sum(function (ProxyFile $proxyFile) {
            return $proxyFile->aliases()->count();
        });

        $hits = $proxyFiles->sum(function (ProxyFile $proxyFile) {
            return $proxyFile->hits()->count();
        });

        return view('statistics')
            ->with('size', $size)
            ->with('files', $proxyFiles->count())
            ->with('aliases', $aliases)
            ->with('hits', $hits);
    }
}
