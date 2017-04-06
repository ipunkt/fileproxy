<?php

namespace App\Http\Controllers;

use App\ProxyFile;
use Ramsey\Uuid\Uuid;
use App\Jobs\CreateLocalFile;
use App\Jobs\UpdateProxyFile;
use App\Http\Requests\UpdateFileRequest;
use App\Http\Requests\UploadFileRequest;

class FileController extends Controller
{
    public function create()
    {
        return view('file.create');
    }

    public function store(UploadFileRequest $request)
    {
        $reference = Uuid::uuid4();

        $this->dispatch(new CreateLocalFile($reference, $request->file('file')));

        return redirect()->route('file.show', ['file' => $reference]);
    }

    public function show(string $file)
    {
        $proxyFile = ProxyFile::byReference($file);

        $aliases = $proxyFile->aliases;

        return view('file.show')
            ->with('proxyFile', $proxyFile)
            ->with('aliases', $aliases);
    }

    public function update(UpdateFileRequest $request, string $file)
    {
        $proxyFile = ProxyFile::byReference($file);

        $this->dispatch(new UpdateProxyFile($proxyFile, $request->get('filename')));

        return redirect()->route('file.show', ['file' => $file]);
    }
}
