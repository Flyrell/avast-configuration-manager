<?php

namespace App\Parser\Config\FormatParser;

use DOMNode;
use DOMDocument;
use DOMNodeList;
use App\Dto\ConfigDto;
use App\Enum\ConfigKeysEnum;
use App\Exception\ParsingException;
use App\Parser\Config\ConfigInterface;

class XMLFormatParser implements FormatParserInterface
{

    public function __construct(private string $xmlConfigSchemaFilepath) {}

    public function parse(string $content): ConfigInterface
    {
        /** @var DOMDocument $dom */
        [ $dom, $result ] = $this->parseXMLContent($content);
        if ($result === false) {
            throw new ParsingException(ParsingException::FAILED_TO_PARSE_INPUT);
        }

        $configNode = $dom->getElementsByTagName('config')->item(0);

        $config = new ConfigDto();

        /** @var DOMNode $node */
        foreach ($configNode->childNodes->getIterator() as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $value = $this->parseNode($node);
            if (!is_null($value)) {
                $config->set($node->nodeName, $value);
            }
        }

        var_dump($config);

        return $config;
    }

    public function supports(string $content): bool
    {
        [ $dom, $result ] = $this->parseXMLContent($content);
        if ($result === false) {
            return false;
        }

        return $dom->schemaValidate($this->xmlConfigSchemaFilepath);
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

    private function parseNode(DOMNode $node): mixed
    {
        return match ($node->nodeName) {
            ConfigKeysEnum::SUBDOMAINS => $this->parseSubdomains($node->childNodes),
            ConfigKeysEnum::COOKIES => $this->parseCookies($node->childNodes),
            default => null,
        };
    }

    private function parseSubdomains(DOMNodeList $nodeList): array
    {
        $subdomains = [];

        /** @var DOMNode $node */
        foreach ($nodeList->getIterator() as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            if (!empty($node->nodeValue)) {
                $subdomains[] = $node->nodeValue;
            }
        }

        return array_unique($subdomains);
    }

    private function parseCookies(DOMNodeList $nodeList): array
    {
        $cookies = [];

        /** @var DOMNode $node */
        foreach ($nodeList->getIterator() as $node) {
            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $name = $node->attributes->getNamedItem('name')->textContent;
            $host = $node->attributes->getNamedItem('host')->textContent;

            if (!empty($name) && !empty($host) && !empty($node->nodeValue)) {
                $cookies["cookie:{$name}:{$host}"] = $node->nodeValue;
            }
        }

        return $cookies;
    }
}
