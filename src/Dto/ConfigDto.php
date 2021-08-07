<?php

namespace App\Dto;

use DateInterval;
use App\Enum\CacheTagsEnum;
use App\Parser\ConfigInterface;
use App\Cache\Collection\CacheableCollection;
use App\Cache\Collection\CacheableCollectionItemInterface;

class ConfigDto extends CacheableCollection implements ConfigInterface
{

    /**
     * @inheritDoc
     */
    public function set(string $key, mixed $value): ConfigInterface
    {
        if ($value instanceof CacheableCollectionItemInterface) {
            $this->add($value);
        }
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function get(string $key): ?CacheableCollectionItemInterface
    {
        return $this->items[$key] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getTags(): array
    {
        return [ CacheTagsEnum::CONFIG ];
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter(): DateInterval
    {
        return DateInterval::createFromDateString('+3 hours');
    }
}
