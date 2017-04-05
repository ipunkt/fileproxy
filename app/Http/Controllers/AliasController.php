<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAliasRequest;
use App\Jobs\CreateFileAlias;
use App\ProxyFile;
use Carbon\Carbon;

class AliasController extends Controller
{
    public function store(CreateAliasRequest $request, string $file)
    {
        $proxyFile = ProxyFile::byReference($file);

        $from = Carbon::now();
        if ($request->get('from') !== null) {
            $from = Carbon::parse($request->get('from'));
        }
        $until = null;
        if ($request->get('until') !== null) {
            $until = Carbon::parse($request->get('until'));
        }
        $hits = null;
        if (intval($request->get('hits')) > 0) {
            $hits = intval($request->get('hits'));
        }

        $this->dispatch(new CreateFileAlias($proxyFile, $request->get('path'), $hits, $from, $until));

        return redirect()->route('file.show', ['file' => $file]);
    }
}
