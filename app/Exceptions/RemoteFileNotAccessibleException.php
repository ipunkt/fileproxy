<?php

namespace App\Exceptions;

class RemoteFileNotAccessibleException extends \Exception
{
    /**
     * @var string
     */
    private $url;

    /**
     * constructing RemoteFileNotAccessibleException
     *
     * @param string $url
     */
    public function __construct(string $url)
    {
        $this->url = $url;

        parent::__construct('Remote file ' . $this->url . ' is not accessible.');
    }

    public function url(): string
    {
        return $this->url;
    }
}