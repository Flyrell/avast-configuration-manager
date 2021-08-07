<?php

namespace App\Cache\Collection;

class CacheableCollectionItem implements CacheableCollectionItemInterface
{

    public function __construct(protected string $key, protected mixed $value) {}

    /**
     * @inheritDoc
     */
    public function getCacheKey(): string
    {
        return $this->key;
    }

    /**
     * @inheritDoc
     */
    public function getCacheValue(): mixed
    {
        return $this->value;
    }
}
