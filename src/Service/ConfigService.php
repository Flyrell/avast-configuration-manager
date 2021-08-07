<?php

namespace App\Service;

use App\Dto\ConfigDto;
use App\Cache\CacheInterface;
use App\Exception\ParsingException;
use App\Parser\ConfigParserInterface;
use App\Exception\ConfigLoadException;

class ConfigService
{

    public function __construct(private CacheInterface $cache, private ConfigParserInterface $configParser) {}

    /**
     * Loads config from files and saves it to cache.
     *
     * @param string $filepath
     * @throws ConfigLoadException
     */
    public function loadFromFile(string $filepath): void
    {
        try {
            $config = $this->configParser->parseFile($filepath);
            $this->save($config);
        } catch (ParsingException $e) {
            throw new ConfigLoadException(0, $e);
        }
    }

    /**
     * Saves config to cache.
     *
     * @param ConfigDto $config
     */
    private function save(ConfigDto $config)
    {
        $this->cache->saveCollection($config);
    }
}
