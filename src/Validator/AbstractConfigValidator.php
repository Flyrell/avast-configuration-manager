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
