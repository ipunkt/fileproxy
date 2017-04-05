<?php

namespace App;

use App\Interfaces\Sendable;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ServableFile implements Sendable
{
    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var array
     */
    private $headers = [];

    public function __construct(string $file, string $filename, string $mimetype, string $checksum)
    {
        $this->file = $file;
        $this->filename = $filename;
        $this->headers['Content-Type'] = $mimetype;
        $this->headers['E-Tag'] = $checksum;
    }

    /**
     * send file to client
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function send(): BinaryFileResponse
    {
        return response()->download($this->file, $this->filename, $this->headers);
    }
}