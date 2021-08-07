<?php

namespace App\Parser\XMLNodeParser;

use DOMNode;
use App\Utils\DOMUtils;
use App\Dto\SubdomainsDto;
use App\Enum\ConfigKeysEnum;

class SubdomainsParser implements XMLNodeParserInterface
{

    /**
     * @inheritDoc
     */
    public function parse(DOMNode $node): iterable
    {
        $subdomains = new SubdomainsDto(ConfigKeysEnum::SUBDOMAINS, []);
        DOMUtils::iterateElements($node->childNodes, function ($node) use (&$subdomains) {
            /** @var DOMNode $node */
            if (!empty($node->nodeValue)) {
                $subdomains->add($node->nodeValue);
            }
        });

        return [ $subdomains ];
    }

    /**
     * @inheritDoc
     */
    public function supports(DOMNode $node): bool
    {
        return $node->nodeName === ConfigKeysEnum::SUBDOMAINS;
    }
}
