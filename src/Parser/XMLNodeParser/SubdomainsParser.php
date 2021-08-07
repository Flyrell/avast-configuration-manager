<?php

namespace App\Parser\XMLNodeParser;

use DOMNode;
use App\Utils\DOMUtils;
use App\Enum\ConfigKeysEnum;

class SubdomainsParser implements XMLNodeParserInterface
{

    /**
     * @inheritDoc
     */
    public function parse(DOMNode $node): array
    {
        $subdomains = [];
        DOMUtils::iterateElements($node->childNodes, function ($node) use (&$subdomains) {
            /** @var DOMNode $node */
            if (!empty($node->nodeValue)) {
                $subdomains[] = $node->nodeValue;
            }
        });

        return array_unique($subdomains);
    }

    /**
     * @inheritDoc
     */
    public function supports(DOMNode $node): bool
    {
        return $node->nodeName === ConfigKeysEnum::SUBDOMAINS;
    }
}
