<?php

namespace App\Exceptions;

class FileAliasCanNotBeServed extends \Exception
{
    public static function noHitsLeft(): self
    {
        return new static('File can not be served, because no hits left.');
    }

    public static function notVisible(): self
    {
        return new static('File can not be served, because it is not visible right now.');
    }
}
