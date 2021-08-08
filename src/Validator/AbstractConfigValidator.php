<?php

namespace App\Validator;

/**
 * May seem a bit weird to have a public constructor on an abstract class,
 * but it saves us some time when setting the child classes as there's
 * no need to create public constructors that call the parent constructor.
 *
 * As long as PHP allows that, I guess it's a valid walk-around :-)
 */
abstract class AbstractConfigValidator implements ConfigValidatorInterface
{

    /**
     * @param array $configSchema
     */
    public function __construct(protected array $configSchema) {}

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
    protected function validateRecursively(array $mapping, callable $fn, array $parents = []): void
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
     * Validates if the properties of given object type are correct according to schema.
     *
     * @param array $properties
     * @param array $propertyMapping
     * @return bool
     */
    protected function validateObjectProperties(array $properties, array $propertyMapping): bool
    {
        foreach ($propertyMapping['properties'] as $name => $property) {
            $actualType = $properties[$name] ?? null;
            $isOptional = (bool) ($property['optional'] ?? false);
            if (is_null($actualType) && $isOptional) {
                continue;
            }
            if ($actualType !== $property['type']) {
                return false;
            }
        }

        return true;
    }

    /**
     * @inheritDoc
     */
    abstract public function validate(mixed $content): bool;
}
