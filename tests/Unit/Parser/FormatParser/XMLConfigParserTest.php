<?php

namespace App\Tests\Unit\Parser\FormatParser;

use App\Dto\ConfigDto;
use App\Exception\ParsingException;
use App\Validator\ConfigValidatorInterface;
use App\Tests\Unit\Utils\ConfigParserUtils;
use App\Parser\FormatParser\XMLFormatParser;
use PHPUnit\Framework\MockObject\MockObject;
use App\Parser\FormatParser\FormatParserInterface;
use App\Parser\XMLNodeParser\XMLNodeParserInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class XMLConfigParserTest extends KernelTestCase
{

    /** @var XMLNodeParserInterface|MockObject[] $nodeParsers */
    private array $nodeParsers;

    private ConfigValidatorInterface $configValidator;

    protected function setUp(): void
    {
        $this->nodeParsers = [
            $this->getMockForAbstractClass(XMLNodeParserInterface::class),
            $this->getMockForAbstractClass(XMLNodeParserInterface::class),
        ];
        $this->configValidator = $this->getMockForAbstractClass(ConfigValidatorInterface::class);
    }

    public function testShouldImplementConfigParserInterface(): void
    {
        $parser = new XMLFormatParser($this->nodeParsers, $this->configValidator);
        $this->assertInstanceOf(FormatParserInterface::class, $parser);
    }

    public function testShouldSupportCorrectXMLContent(): void
    {
        $this->configValidator->expects($this->once())
            ->method('validate')
            ->willReturn(true);

        $parser = new XMLFormatParser($this->nodeParsers, $this->configValidator);
        $result = $parser->supports(ConfigParserUtils::$supportedXMLContent);

        $this->assertTrue($result);
    }

    public function testShouldNotSupportIncorrectXMLContent(): void
    {
        $this->configValidator->expects($this->once())
            ->method('validate')
            ->willReturn(false);

        $parser = new XMLFormatParser($this->nodeParsers, $this->configValidator);
        $result = $parser->supports(ConfigParserUtils::$unsupportedXMLContent);

        $this->assertFalse($result);
    }

    public function testShouldParseSupportedXMLContent(): void
    {
        $nodeParserResult = 'NODE_PARSER_RESULT';
        $this->nodeParsers[0]->expects($this->atLeastOnce())
            ->method('supports')
            ->willReturn(true);
        $this->nodeParsers[0]->expects($this->atLeastOnce())
            ->method('parse')
            ->willReturn($nodeParserResult);

        $parser = new XMLFormatParser($this->nodeParsers, $this->configValidator);

        try {
            $result = $parser->parse(ConfigParserUtils::$supportedXMLContent);
        } catch (ParsingException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }

        $this->assertInstanceOf(ConfigDto::class, $result);
        $this->assertEquals($result->get('subdomains'), $nodeParserResult);
    }

    /**
     * Uses the $wrongXMLContent as the input, as the unsupported one is 100% correct, but is not supported.
     * Support for the content is determined in the ConfigParserInterface::supports() method
     * which should be called when the business logic determines what parser to use.
     *
     * Therefore, calling ConfigParserInterface::supports() method again within the parse method itself
     * would result in unnecessary reduce in speed and call duplicity (at least with the current business logic).
     *
     * @throws ParsingException
     */
    public function testShouldNotParseUnsupportedXMLContent(): void
    {
        $this->nodeParsers[0]->expects($this->never())
            ->method('supports');
        $this->nodeParsers[0]->expects($this->never())
            ->method('parse');

        $parser = new XMLFormatParser($this->nodeParsers, $this->configValidator);

        $this->expectException(ParsingException::class);
        $parser->parse(ConfigParserUtils::$wrongXMLContent);
    }

    public function testShouldNotValidateWhenContentIsNotParsed(): void
    {
        $this->configValidator->expects($this->never())
            ->method('validate');

        $parser = new XMLFormatParser($this->nodeParsers, $this->configValidator);
        $result = $parser->supports(ConfigParserUtils::$wrongXMLContent);

        $this->assertFalse($result);
    }

    public function testShouldUseCorrectNodeParser(): void
    {
        $this->nodeParsers[0]->expects($this->atLeastOnce())
            ->method('supports')
            ->willReturn(false);
        $this->nodeParsers[0]->expects($this->never())
            ->method('parse');

        $parserResult = 'NODE_PARSER_RESULT';
        $this->nodeParsers[1]->expects($this->atLeastOnce())
            ->method('supports')
            ->willReturn(true);
        $this->nodeParsers[1]->expects($this->atLeastOnce())
            ->method('parse')
            ->willReturn($parserResult);

        $parser = new XMLFormatParser($this->nodeParsers, $this->configValidator);
        $result = $parser->parse(ConfigParserUtils::$supportedXMLContent);

        $this->assertInstanceOf(ConfigDto::class, $result);
        $this->assertEquals($result->get('cookies'), $parserResult);
        $this->assertEquals($result->get('subdomains'), $parserResult);
    }
}
