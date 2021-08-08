<?php

namespace App\Logger;

use Monolog\Handler\AbstractProcessingHandler;

class DisableableLogger extends AbstractProcessingHandler implements DisableableLoggerInterface
{
    protected bool $loggingEnabled = true;

    public function enableLogging(): void
    {
        $this->loggingEnabled = true;
    }

    public function disableLogging(): void
    {
        $this->loggingEnabled = false;
    }

    protected function write(array $record): void
    {
        if ($this->loggingEnabled) {
            echo ($record['message'] ?? '') . PHP_EOL;
        }
    }
}
