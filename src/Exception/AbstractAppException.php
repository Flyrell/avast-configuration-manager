<?php

namespace App\Exception;

use Exception;
use Throwable;
use function sprintf;

abstract class AbstractAppException extends Exception
{

    /** @var string[] $messages */
    protected static array $messages = [];

    /**
     * May seem a bit weird to have a public constructor on an abstract class,
     * but it saves us some time when setting the child exceptions as there's
     * no need to create public constructors that call the parent constructor.
     *
     * @param int $code
     * @param array $messageContents
     * @param Throwable|null $previous
     */
    public function __construct($code, array $messageContents = [], Throwable $previous = null)
    {
        $message = $this->findMessage($code);
        parent::__construct(sprintf($message, ...$messageContents), $code, $previous);
    }

    /**
     * Finds the message string in a $messages property in a class.
     *
     * @param int $code
     * @return string
     */
    private function findMessage(int $code): string
    {
        return static::$messages[$code] ?? 'Unknown error';
    }
}
