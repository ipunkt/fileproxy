<?php

namespace App\Traits;

trait FileSeparationConcern
{
    protected function getPathSeparated($key): string
    {
        $parts = array_slice(str_split($hash = sha1($key), 2), 0, 2);

        return implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $hash;
    }
}