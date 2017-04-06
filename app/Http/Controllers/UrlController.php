<?php

namespace App\Http\Controllers;

use App\ProxyFile;
use Ramsey\Uuid\Uuid;
use App\Jobs\CreateRemoteFile;
use App\Http\Requests\CreateUrlRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UrlController extends Controller
{
    public function create()
    {
        return view('url.create');
    }

    public function store(CreateUrlRequest $request)
    {
        $reference = Uuid::uuid4();

        $this->dispatch(new CreateRemoteFile($reference, $request->get('url')));

        return redirect()->route('url.show', ['url' => $reference]);
    }

    public function show($url)
    {
        try {
            $proxyFile = ProxyFile::byReference($url);
            if ($proxyFile->remoteFile !== null) {
                return redirect()->route('file.show', ['file' => $url]);
            }
        } catch (ModelNotFoundException $exception) {
        }

        return view('url.show')
            ->with('reference', $url);
    }
}
