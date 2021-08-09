<?php

namespace App\Exception;

class ValidationException extends AbstractAppException
{

    public const VALIDATION_FAILED = 3001;

    /** @var string[] $messages */
    protected static array $messages = [
        self::VALIDATION_FAILED => 'Validation failed.',
    ];
}
