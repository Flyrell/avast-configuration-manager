<?php

namespace App\Cache\Collection;

use DateInterval;
use function in_array;
use function array_values;

class CacheableCollection implements CacheableCollectionInterface
{

    /** @var CacheableCollectionItemInterface[]  */
    protected array $items;

    /** @var string[] $tags */
    protected array $tags;

    /** @var DateInterval $expiresAfter */
    protected DateInterval $expiresAfter;

    /**
     * @inheritDoc
     */
    public function add(CacheableCollectionItemInterface $item): self
    {
        $this->items[$item->getCacheKey()] = $item;
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
    public function getAll(): iterable
    {
        return array_values($this->items);
    }

    /**
     * @inheritDoc
     */
    public function addTag(string $tag): self
    {
        if (!in_array($tag, $this->tags)) {
            $this->tags[] = $tag;
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getTags(): array
    {
        return $this->tags;
    }

    /**
     * @inheritDoc
     */
    public function setExpiresAfter(DateInterval|string $interval): self
    {
        if (!($interval instanceof DateInterval)) {
            $interval = DateInterval::createFromDateString($interval);
        }

        $this->expiresAfter = $interval;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function expiresAfter(): DateInterval
    {
        return $this->expiresAfter;
    }
}
