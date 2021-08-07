<?php

namespace App\Parser\FormatParser;

use DOMNode;
use DOMDocument;
use App\Dto\ConfigDto;
use App\Utils\DOMUtils;
use App\Parser\ConfigInterface;
use App\Exception\ParsingException;
use App\Validator\ConfigValidatorInterface;
use App\Parser\XMLNodeParser\XMLNodeParserInterface;

class XMLFormatParser implements FormatParserInterface
{

    /**
     * @param XMLNodeParserInterface[] $nodeParsers
     * @param ConfigValidatorInterface $configValidator
     */
    public function __construct(private array $nodeParsers, private ConfigValidatorInterface $configValidator) {}

    /**
     * @inheritDoc
     */
    public function parse(string $content): ConfigInterface
    {
        /** @var DOMDocument $dom */
        [ $dom, $result ] = $this->parseXMLContent($content);
        if ($result === false) {
            throw new ParsingException(ParsingException::FAILED_TO_PARSE_INPUT);
        }

        $config = new ConfigDto();
        $configNode = $dom->getElementsByTagName('config')->item(0);
        DOMUtils::iterateElements($configNode->childNodes, function ($node) use (&$config) {
            $value = $this->parseNode($node);
            if (!is_null($value)) {
                $config->set($node->nodeName, $value);
            }
        });

        return $config;
    }

    /**
     * @inheritDoc
     */
    public function supports(string $content): bool
    {
        [ $dom, $result ] = $this->parseXMLContent($content);
        if ($result === false) {
            return false;
        }

        return $this->configValidator->validate($dom);
    }

    /**
     * Returns DOMDocument as first element and result of loading as the second element.
     *
     * @param string $content
     * @return array
     */
    private function parseXMLContent(string $content): array
    {
        $dom = new DOMDocument();
        return [ $dom, $dom->loadXML($content) ];
    }

    /**
     * Determines which Parser to use when parsing the node.
     *
     * @param DOMNode $node
     * @return mixed
     */
    private function parseNode(DOMNode $node): mixed
    {
        foreach ($this->nodeParsers as $nodeParser) {
            if ($nodeParser->supports($node)) {
                return $nodeParser->parse($node);
            }
        }
        return null;
    }
}
