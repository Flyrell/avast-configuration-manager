<?php

namespace App\Service;

use App\Exception\ParsingException;
use App\Parser\ConfigParserInterface;
use App\Exception\ConfigLoadException;

class ConfigLoaderService
{

    public function __construct(private ConfigParserInterface $configParser) {}

    /**
     * @param string $filepath
     * @throws ConfigLoadException
     */
    public function loadFromFile(string $filepath): void
    {
        try {
            $config = $this->configParser->parseFile($filepath);
        } catch (ParsingException $e) {
            throw new ConfigLoadException(0, $e);
        }
    }
}