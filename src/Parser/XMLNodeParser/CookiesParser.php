<?php

namespace App\Parser\XMLNodeParser;

use DOMNode;
use App\Utils\DOMUtils;
use App\Enum\ConfigKeysEnum;

class CookiesParser implements XMLNodeParserInterface
{

    /**
     * @inheritDoc
     */
    public function parse(DOMNode $node): array
    {
        $cookies = [];
        DOMUtils::iterateElements($node->childNodes, function ($node) use (&$cookies) {
            /** @var DOMNode $node */
            $name = $node->attributes->getNamedItem('name')->textContent;
            $host = $node->attributes->getNamedItem('host')->textContent;
            if (!empty($name) && !empty($host) && !empty($node->nodeValue)) {
                $cookies["cookie:{$name}:{$host}"] = $node->nodeValue;
            }
        });

        return $cookies;
    }

    /**
     * @inheritDoc
     */
    public function supports(DOMNode $node): bool
    {
        return $node->nodeName === ConfigKeysEnum::COOKIES;
    }
}
