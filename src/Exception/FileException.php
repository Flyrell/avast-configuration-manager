<?php

namespace App\Exception;

class FileException extends AbstractAppException
{

    const FILE_NOT_FOUND = 1001;
    const FILE_UNREADABLE = 1002;
    const MIME_TYPE_NOT_FOUND = 1003;

    /** @var string[] $messages */
    protected static array $messages = [
        self::FILE_NOT_FOUND => 'File not found.',
        self::FILE_UNREADABLE => 'Unable to read file contents.',
        self::MIME_TYPE_NOT_FOUND => 'Unable to read file\'s MIME type.',
    ];
}
