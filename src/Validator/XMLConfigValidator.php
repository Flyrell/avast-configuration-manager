<?php

namespace App\Validator;

use DOMNode;
use DOMDocument;
use App\Utils\DOMUtils;

/**
 * Validates XML Config file
 */
class XMLConfigValidator extends AbstractConfigValidator
{

    /**
     * @inheritDoc
     */
    public function validate(mixed $content): bool
    {
        if (!($content instanceof DOMDocument)) {
            return false;
        }

        $rootNode = $content->getElementsByTagName('config')->item(0);
        $currentNode = $rootNode;
        $isValid = true;
        $this->validateRecursively(
            $this->configSchema,
            function ($key, $propertyMapping, $nestingLevel) use (&$currentNode, &$rootNode, &$isValid) {
                if ($nestingLevel === 0) {
                    $currentNode = $rootNode;
                }

                $nodeFound = false;
                $isOptional = $propertyMapping['optional'] ?? false;

                foreach (($currentNode?->childNodes?->getIterator() ?? []) as $node) {
                    /** @var DOMNode $node */

                    // Skip if the node is not element
                    if ($node->nodeType !== XML_ELEMENT_NODE) {
                        continue;
                    }

                    // If the node is not the one we're looking for
                    if ($node->nodeName !== $key && $key !== 'child') {
                        continue;
                    }

                    $nodeFound = true;
                    $currentNode = $node;
                    if (!$this->validateNode($node, $propertyMapping)) {
                        $isValid = false;
                    }
                }

                if (!$isOptional && !$nodeFound) {
                    $isValid = false;
                }

                return $nodeFound;
            }
        );

        return $isValid;
    }

    /**
     * Validates config mapping recursively and runs the provided callback for each element.
     * Callback arguments (from first to last):
     *  - property name
     *  - property mapping
     *  - nesting level
     *
     * Callback should return true if the element should continue nesting recursively.
     *
     * @param array $mapping
     * @param callable $fn
     * @param array $parents
     */
    private function validateRecursively(array $mapping, callable $fn, array $parents = []): void
    {
        foreach ($mapping as $key => $propertyMapping) {
            $found = $fn($key, $propertyMapping, count($parents));
            $type = $propertyMapping['type'] ?? null;
            if ($found && $type === 'array') {
                $this->validateRecursively([ 'child' => $propertyMapping['child'] ], $fn, [ ...$parents, $key ]);
            }
        }
    }

    /**
     * Validates information from node against the config mapping.
     *
     * @param DOMNode $node
     * @param array $propertyMapping
     * @return bool
     */
    private function validateNode(DOMNode $node, array $propertyMapping): bool
    {
        $actualType = DOMUtils::determineNodeType($node, $propertyMapping['type']);

        if ($propertyMapping['type'] !== $actualType) {
            return false;
        }

        if ($actualType === 'object') {
            $properties = DOMUtils::extractDataFromNode($node, fn($value) => gettype($value));
            if (!$this->validateObjectProperties($properties, $propertyMapping)) {
                return false;
            }
        }

        return true;
    }
}
