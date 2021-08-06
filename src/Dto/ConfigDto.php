<?php

namespace App\Dto;

use App\Parser\Config\ConfigInterface;

class ConfigDto implements ConfigInterface
{

    /** @var array $configuration */
    private array $configuration = [];

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value): ConfigInterface
    {
        $this->configuration[$key] = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): mixed
    {
        return $this->configuration[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function exists(string $key): bool
    {
        return key_exists($key, $this->configuration);
    }
}
