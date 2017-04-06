<?php

namespace App\Http\Controllers;

use App\ProxyFile;
use Carbon\Carbon;
use App\Jobs\CreateFileAlias;
use App\Jobs\DeleteFileAlias;
use App\Http\Requests\CreateAliasRequest;

class AliasController extends Controller
{
    /**
     * create a new file alias.
     *
     * @param CreateAliasRequest $request
     * @param string $file
     * @return \Illuminate\Http\RedirectResponse
     */
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

    public function destroy(string $file, string $alias)
    {
        $proxyFile = ProxyFile::byReference($file);

        $fileAlias = $proxyFile->aliases()->whereId($alias)->firstOrFail();

        $this->dispatch(new DeleteFileAlias($fileAlias));

        return redirect()->route('file.show', ['file' => $file]);
    }
}
