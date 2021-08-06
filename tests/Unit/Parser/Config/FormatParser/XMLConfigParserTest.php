<?php

namespace App\Tests\Unit\Parser\Config\FormatParser;

use App\Dto\ConfigDto;
use App\Exception\ParsingException;
use App\Tests\Unit\Utils\ConfigParserUtils;
use App\Parser\Config\FormatParser\XMLFormatParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Parser\Config\FormatParser\FormatParserInterface;

class XMLConfigParserTest extends KernelTestCase
{

    private XMLFormatParser $parser;

    private static string $xmlConfigSchemaFilepath = __DIR__ . '/../../../../../config/schemas/config.xsd';

    protected function setUp(): void
    {
        $this->parser = new XMLFormatParser(self::$xmlConfigSchemaFilepath);
    }

    public function testShouldImplementConfigParserInterface(): void
    {
        $this->assertInstanceOf(FormatParserInterface::class, $this->parser);
    }

    public function testShouldSupportCorrectXMLContent(): void
    {
        $result = $this->parser->supports(ConfigParserUtils::$supportedXMLContent);

        $this->assertTrue($result);
    }

    public function testShouldNotSupportIncorrectXMLContent(): void
    {
        $result = $this->parser->supports(ConfigParserUtils::$unsupportedXMLContent);

        $this->assertFalse($result);
    }

    public function testShouldParseSupportedXMLContent(): void
    {
        try {
            $result = $this->parser->parse(ConfigParserUtils::$supportedXMLContent);
        } catch (ParsingException $e) {
            $this->fail("Exception should not have been thrown. {$e->getMessage()}");
        }

        $this->assertInstanceOf(ConfigDto::class, $result);
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
        $this->expectException(ParsingException::class);
        $this->parser->parse(ConfigParserUtils::$wrongXMLContent);
    }
}
