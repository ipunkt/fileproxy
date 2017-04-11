<?php

namespace App;

use App\Interfaces\Sendable;

class ServableRemoteFile implements Sendable
{
    /**
     * @var \App\RemoteFile
     */
    private $remoteFile;

    /**
     * @var array
     */
    private $headers = [];

    public function __construct(RemoteFile $remoteFile, string $filename, string $mimetype, string $checksum)
    {
        $this->remoteFile = $remoteFile;
        $this->headers['Content-Type'] = $mimetype;
        $this->headers['Content-Length'] = $remoteFile->proxyFile->size;
        $this->headers['Content-Disposition'] = 'attachment; filename="' . $filename . '"';
        $this->headers['E-Tag'] = $checksum;
    }

    public function send()
    {
        $fp = fopen($this->remoteFile->url, 'rb');

        foreach ($this->headers as $header => $value) {
            header($header . ': ' . $value, true);
        }

        fpassthru($fp);
        exit;
    }
}
