<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateFileRequest;
use App\Jobs\CreateLocalFile;
use App\Jobs\CreateRemoteFile;
use App\ProxyFile;
use App\Transformers\ProxyFileTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Mimey\MimeTypes;
use Ramsey\Uuid\Uuid;

class FileController extends ApiController
{
    public function store(CreateFileRequest $request)
    {
        $reference = Uuid::uuid4();

        if ($request->isAttachment()) {
            $content = $request->source();
            $data = $request->get('data', []);
            $filename = array_get($data, 'attributes.filename');

            $mimetypes = new MimeTypes();
            $parts = explode('.', $filename);
            $extension = last($parts);
            $mimetype = $mimetypes->getMimeType($extension);

            $tempFilename = tempnam(sys_get_temp_dir(), 'proxyfile_');
            file_put_contents($tempFilename, $content);

            $job = new CreateLocalFile($reference, new UploadedFile($tempFilename, $filename, $mimetype));
        } else {
            $url = $request->source();
            $job = new CreateRemoteFile($reference, $url);
        }

        $this->dispatch($job);

        try {
            $proxyFile = ProxyFile::byReference($reference);
        } catch (ModelNotFoundException $exception) {
            $proxyFile = new ProxyFile([
                'reference' => $reference,
                'filename' => $request->filename(),
            ]);
        }

        return $this->respondCreated($proxyFile, new ProxyFileTransformer(), 'files');
    }
}
