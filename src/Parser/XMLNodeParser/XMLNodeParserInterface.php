<?php

namespace App\Parser\XMLNodeParser;

use DOMNode;

interface XMLNodeParserInterface
{

    /**
     * Extracts data from the provided node.
     *
     * @param DOMNode $node
     * @return mixed
     */
    public function parse(DOMNode $node): iterable;

    /**
     * Determines if the parser supports provided node.
     *
     * @param DOMNode $node
     * @return bool
     */
    public function supports(DOMNode $node): bool;
}
