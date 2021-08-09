<?php

namespace App\Service;

use App\Dto\ConfigDto;
use App\Cache\CacheInterface;
use App\Exception\CacheException;
use App\Exception\ParsingException;
use App\Parser\ConfigParserInterface;

class ConfigService
{

    public function __construct(private CacheInterface $cache, private ConfigParserInterface $configParser) {}

    /**
     * Loads config from files and saves it to cache.
     *
     * @param string $filepath
     * @return iterable
     * @throws CacheException
     * @throws ParsingException
     */
    public function loadFromFile(string $filepath): iterable
    {

        /** @var ConfigDto $config */
        $config = $this->configParser->parseFile($filepath);
        foreach ($this->cache->saveCollection($config) as $savedKey) {
            yield $savedKey;
        }
    }
}
