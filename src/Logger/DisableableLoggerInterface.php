<?php

namespace App\Logger;

interface DisableableLoggerInterface
{

    public function enableLogging(): void;

    public function disableLogging(): void;
}
