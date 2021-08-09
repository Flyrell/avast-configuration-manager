<?php

namespace App\Exception;

class CacheException extends AbstractAppException
{

    public const COULD_NOT_SAVE = 4001;

    protected static array $messages = [
        self::COULD_NOT_SAVE => 'Could not save key "%s" to cache.',
    ];
}
