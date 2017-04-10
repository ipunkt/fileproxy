<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CreateFileRequest;
use App\Jobs\CreateLocalFile;
use App\Jobs\CreateRemoteFile;
use App\ProxyFile;
use App\Transformers\FileAliasTransformer;
use App\Transformers\ProxyFileTransformer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Mimey\MimeTypes;
use Ramsey\Uuid\Uuid;

class FilesController extends ApiController
{
    /**
     * store a new file or remote url.
     *
     * @param CreateFileRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateFileRequest $request)
    {
        $reference = Uuid::uuid4();

        if ($request->isAttachment()) {
            $content = $request->source();
            $filename = $request->filename();

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

    /**
     * displays the attributes for a proxy file.
     *
     * @param Manager $fractal
     * @param Request $request
     * @param string $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Manager $fractal, Request $request, string $file)
    {
        $proxyFile = ProxyFile::byReference($file);

        if ($request->has('include')) {
            $fractal->parseIncludes($request->get('include'));
        }

        $resource = new Item($proxyFile, new ProxyFileTransformer(), 'files');
        $data = $fractal->createData($resource)->toArray();

        return $this->respondData($data, 200);
    }

    /**
     * show all aliases by proxy file
     *
     * @param string $file
     * @return \Illuminate\Http\JsonResponse
     */
    public function showAliases(string $file)
    {
        $proxyFile = ProxyFile::byReference($file);

        return $this->respondCollection($proxyFile->aliases, new FileAliasTransformer(), 'aliases');
    }
}
