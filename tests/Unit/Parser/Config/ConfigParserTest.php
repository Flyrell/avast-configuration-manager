<?php

namespace App\Tests\Unit\Parser\Config;

use App\Dto\ConfigDto;
use ReflectionException;
use App\Service\FileService;
use App\Exception\ParsingException;
use App\Parser\Config\ConfigParser;
use App\Parser\Config\ConfigInterface;
use App\Tests\Unit\Utils\ReflectionUtils;
use App\Tests\Unit\Utils\ConfigParserUtils;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Parser\Config\FormatParser\FormatParserInterface;

class ConfigParserTest extends KernelTestCase
{

    private FileService $fileService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fileService = $this->getMockBuilder(FileService::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    public function testShouldSuccessfullyParseSupportedXMLFile(): void
    {
        $parser = $this->getMockForAbstractClass(FormatParserInterface::class);
        $parser->expects($this->once())->method('supports')->willReturn(true);

        $filepath = 'filepath/example.xml';
        $this->fileService
            ->expects($this->once())
            ->method('read')
            ->willReturn('parsed content')
            ->with($filepath);

        $service = new ConfigParser([ $parser ], $this->fileService);

        try {
            $result = $service->parseFile($filepath);
        } catch (ParsingException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }

        $this->assertInstanceOf(ConfigInterface::class, $result);
    }

    public function testShouldFailToParseUnsupportedXMLFile(): void
    {
        $parser = $this->getMockForAbstractClass(FormatParserInterface::class);
        $parser->expects($this->once())->method('supports')->willReturn(false);

        $filepath = 'filepath/example.xml';
        $this->fileService
            ->expects($this->once())
            ->method('read')
            ->willReturn('parsed content')
            ->with($filepath);

        $service = new ConfigParser([ $parser ], $this->fileService);

        $this->expectException(ParsingException::class);
        $service->parseFile($filepath);
    }

    public function testShouldParseSupportedXMLString(): void
    {
        $parser = $this->getMockForAbstractClass(FormatParserInterface::class);
        $parser->expects($this->once())->method('supports')->willReturn(true);
        $parser->expects($this->once())
            ->method('parse')
            ->willReturn(new ConfigDto())
            ->with(ConfigParserUtils::$supportedXMLContent);

        $service = new ConfigParser([ $parser ], $this->fileService);

        try {
            $result = $service->parseString(ConfigParserUtils::$supportedXMLContent);
        } catch (ParsingException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }

        $this->assertInstanceOf(ConfigDto::class, $result);
    }

    public function testShouldFailToParseUnsupportedXMLString(): void
    {
        $parser = $this->getMockForAbstractClass(FormatParserInterface::class);
        $parser->expects($this->never())->method('parse');
        $parser->expects($this->once())->method('supports')->willReturn(false);

        $service = new ConfigParser([ $parser ], $this->fileService);

        $this->expectException(ParsingException::class);
        $service->parseString(ConfigParserUtils::$unsupportedXMLContent);
    }

    /**
     * Not that it's needed to test that (in my opinion), but just to showcase the skills :-)
     */
    public function testShouldContinueToLookForFirstSupportedParser(): void
    {
        $parser1 = $this->getMockForAbstractClass(FormatParserInterface::class);
        $parser1->expects($this->once())->method('supports')->willReturn(false);

        $parser2 = $this->getMockForAbstractClass(FormatParserInterface::class);
        $parser2->expects($this->once())->method('supports')->willReturn(true);

        $parser3 = $this->getMockForAbstractClass(FormatParserInterface::class);
        $parser3->expects($this->never())->method('supports')->willReturn(true);

        $service = new ConfigParser([
            $parser1,
            $parser2,
            $parser3,
        ], $this->fileService);

        try {
            $result = ReflectionUtils::invokeProtectedOrPrivateMethod($service, 'findParser', ['']);
        } catch (ReflectionException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }

        $this->assertSame($result, $parser2);
    }
}
