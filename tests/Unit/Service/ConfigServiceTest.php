<?php

namespace App\Tests\Unit\Service;

use App\Cache\CacheInterface;
use App\Dto\ConfigDto;
use App\Parser\ConfigParser;
use App\Service\ConfigService;
use App\Exception\ParsingException;
use App\Exception\ConfigLoadException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigServiceTest extends KernelTestCase
{

    private CacheInterface $cache;
    private ConfigParser $configParser;

    protected function setUp(): void
    {
        $this->configParser = $this->getMockBuilder(ConfigParser::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cache = $this->getMockForAbstractClass(CacheInterface::class);
    }

    public function testShouldReadTheFile(): void
    {
        $this->configParser->expects($this->once())
            ->method('parseFile')
            ->willReturn(new ConfigDto());


        $service = new ConfigService($this->cache, $this->configParser);

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

        $service = new ConfigService($this->cache, $this->configParser);

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

        $service = new ConfigService($this->cache, $this->configParser);

        try {
            $service->loadFromFile('filepath.xml');
        } catch (ConfigLoadException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }
    }
}
