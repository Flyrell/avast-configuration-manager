<?php

namespace App\Tests\Unit\Service;

use App\Dto\ConfigDto;
use Psr\Log\LoggerInterface;
use App\Parser\ConfigParser;
use App\Cache\CacheInterface;
use App\Service\ConfigService;
use App\Exception\ParsingException;
use App\Exception\ConfigLoadException;
use App\Cache\Collection\CacheableCollectionItem;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigServiceTest extends KernelTestCase
{

    private CacheInterface $cache;
    private LoggerInterface $logger;
    private ConfigParser $configParser;

    protected function setUp(): void
    {
        $this->configParser = $this->getMockBuilder(ConfigParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cache = $this->getMockForAbstractClass(CacheInterface::class);
        $this->logger = $this->getMockForAbstractClass(LoggerInterface::class);
    }

    public function testShouldReadTheFile(): void
    {
        $this->configParser->expects($this->once())
            ->method('parseFile')
            ->willReturn(new ConfigDto());


        $service = new ConfigService($this->cache, $this->logger, $this->configParser);

        try {
            $service->loadFromFile('filepath.xml');
        } catch (ConfigLoadException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }
    }

    public function testShouldThrowWhenFileReadingFails(): void
    {
        $this->configParser->expects($this->once())
            ->method('parseFile')
            ->willThrowException(new ParsingException(0));

        $service = new ConfigService($this->cache, $this->logger, $this->configParser);

        $this->expectException(ConfigLoadException::class);
        $service->loadFromFile('filepath.xml');
    }

    public function testShouldSaveConfigAfterParsing(): void
    {
        $this->configParser->expects($this->once())
            ->method('parseFile')
            ->willReturn(new ConfigDto());

        $this->cache->expects($this->once())
            ->method('saveCollection');

        $service = new ConfigService($this->cache, $this->logger, $this->configParser);

        try {
            $service->loadFromFile('filepath.xml');
        } catch (ConfigLoadException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }
    }

    public function testShouldLogEachEntryInCollection(): void
    {
        $config = $this->getMockBuilder(ConfigDto::class)
            ->getMock();
        $config->expects($this->once())
            ->method('getAll')
            ->willReturn([
                new CacheableCollectionItem('123', 'value1'),
                new CacheableCollectionItem('234', 'value2'),
            ]);

        $this->configParser->expects($this->once())
            ->method('parseFile')
            ->willReturn($config);

        $this->logger->expects($this->exactly(2))
            ->method('info')
            ->withConsecutive([ '123' ], [ '234' ]);

        $service = new ConfigService($this->cache, $this->logger, $this->configParser);

        try {
            $service->loadFromFile('filepath.xml');
        } catch (ConfigLoadException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }
    }
}
