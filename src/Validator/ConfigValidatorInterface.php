<?php

namespace App\Validator;

interface ConfigValidatorInterface
{

    /**
     * Validates passed content.
     *
     * @param mixed $content
     * @return bool
     */
    public function validate(mixed $content): bool;
}
