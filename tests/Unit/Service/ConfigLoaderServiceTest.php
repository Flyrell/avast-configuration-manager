<?php

namespace App\Tests\Unit\Service;

use App\Dto\ConfigDto;
use App\Exception\ParsingException;
use App\Parser\Config\ConfigParser;
use App\Service\ConfigLoaderService;
use App\Exception\ConfigLoadException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ConfigLoaderServiceTest extends KernelTestCase
{

    private ConfigParser $configParser;

    protected function setUp(): void
    {
        $this->configParser = $this->getMockBuilder(ConfigParser::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testShouldReadTheFile(): void
    {
        $this->configParser->expects($this->once())
            ->method('parseFile')
            ->willReturn(new ConfigDto());

        $service = new ConfigLoaderService($this->configParser);

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

        $service = new ConfigLoaderService($this->configParser);

        $this->expectException(ConfigLoadException::class);
        $service->loadFromFile('filepath.xml');
    }
}
