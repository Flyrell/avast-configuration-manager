<?php

namespace App\Utils;

use DOMAttr;
use DOMNode;
use DOMNodeList;

class DOMUtils
{

    /**
     * As DOMNodeList::getIterator() method all nodes (not only element ones) we can use this function to iterate
     * through the elements only. Function takes callback when first argument is the current DOMNode (Element node).
     *
     * Having fun with the pointers - we can implement the break mechanism to stop the loop even when using callback.
     * When the $break is set to true, the internal foreach will stop.
     *
     * @param DOMNodeList $nodeList
     * @param callable $fn
     * @param bool|null $break
     */
    public static function iterateElements(DOMNodeList $nodeList, callable $fn, ?bool &$break = null): void
    {
        foreach ($nodeList->getIterator() as $node) {
            /** @var DOMNode $node */

            if ($break) {
                break;
            }

            if ($node->nodeType === XML_ELEMENT_NODE) {
                $fn($node);
            }
        }
    }

    /**
     * Iterates the DOMNodeList and returns first element node or null
     *
     * @param DOMNodeList $nodeList
     * @return DOMNode|null
     */
    public static function findFirstElementNode(DOMNodeList $nodeList): ?DOMNode
    {
        $break = false;
        $result = null;
        self::iterateElements($nodeList, function ($node) use (&$result, &$break) {
            /** @var DOMNode $node */
            $result = $node;
            $break = true;
        }, $break);

        return $result;
    }

    /**
     * Converts all attributes with value into a single key-value array.
     * Accepts optional mapping callback as second argument.
     *
     * Callback arguments:
     *  - property value
     *  - property name
     *
     * @param DOMNode $node
     * @param callable|null $mapFn
     * @return array
     */
    public static function extractDataFromNode(DOMNode $node, ?callable $mapFn): array
    {
        $data = [ 'value' => $mapFn($node->nodeValue, 'value') ];
        foreach ($node->attributes->getIterator() as $attribute) {
            /** @var DOMAttr $attribute */
            $data[$attribute->name] = $mapFn ? $mapFn($attribute->value, $attribute->name) : $mapFn;
        }

        return $data;
    }

    /**
     * Counts all the element nodes in DOMNodeList
     *
     * @param DOMNodeList $nodeList
     * @return int
     */
    public static function countElementNodes(DOMNodeList $nodeList): int
    {
        $count = 0;
        self::iterateElements($nodeList, function () use (&$count) {
            $count = $count + 1;
        });
        return $count;
    }

    /**
     * Determines the node type
     *
     * @param DOMNode $node
     * @param string|null $expectedType <- used for arrays, as they may have no elements
     * @return string
     */
    public static function determineNodeType(DOMNode $node, ?string $expectedType = null): string
    {
        if ($node->attributes->count() || $expectedType === 'object') {
            return 'object';
        }
        if (DOMUtils::countElementNodes($node->childNodes) !== 0 || $expectedType === 'array') {
            return 'array';
        }
        return 'string';
    }
}
