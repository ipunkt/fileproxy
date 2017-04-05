<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadFileRequest;
use App\Jobs\CreateLocalFile;
use App\ProxyFile;
use Ramsey\Uuid\Uuid;

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
}
