<?php

namespace App\Exceptions;

class LocalProxyFileCanNotByDeleted extends \Exception
{
    public static function throwException(): self
    {
        return new static('Local proxy file can not be deleted.');
    }
}