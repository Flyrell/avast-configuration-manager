<?php

namespace App\Service;

use App\Dto\ConfigDto;
use Psr\Log\LoggerInterface;
use App\Cache\CacheInterface;
use App\Exception\ParsingException;
use App\Parser\ConfigParserInterface;
use App\Exception\ConfigLoadException;

class ConfigService
{

    public function __construct(
        private CacheInterface $cache,
        private LoggerInterface $logger,
        private ConfigParserInterface $configParser,
    ) {}

    /**
     * Loads config from files and saves it to cache.
     *
     * @param string $filepath
     * @throws ConfigLoadException
     */
    public function loadFromFile(string $filepath): void
    {

        try {
            /** @var ConfigDto $config */
            $config = $this->configParser->parseFile($filepath);
            $this->cache->saveCollection($config);

            foreach ($config->getAll() as $item) {
                $this->logger->info($item->getCacheKey());
            }
        } catch (ParsingException $e) {
            throw new ConfigLoadException(0, $e);
        }
    }
}
