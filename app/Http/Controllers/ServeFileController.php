<?php

namespace App\Http\Controllers;

use App\Jobs\ServeFileAlias;
use Illuminate\Http\Request;

class ServeFileController extends Controller
{
    public function serve(Request $request, string $alias)
    {
        /** @var \App\Interfaces\Sendable $file */
        $file = $this->dispatch(new ServeFileAlias($alias, $request->header('User-Agent')));

        return $file->send();
    }
}
