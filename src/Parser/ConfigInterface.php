<?php

namespace App\Parser;

interface ConfigInterface
{

    /**
     * Sets provided value to configuration object under provided key.
     *
     * @param string $key
     * @param mixed $value
     * @return ConfigInterface
     */
    public function set(string $key, mixed $value): ConfigInterface;

    /**
     * Returns the configured value stored under provided key.
     *
     * @param string $key
     * @return mixed
     */
    public function get(string $key): mixed;
}
