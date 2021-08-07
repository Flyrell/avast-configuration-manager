<?php

namespace App\Dto;

use App\Cache\Collection\CacheableCollectionItem;
use function in_array;
use function json_encode;

class SubdomainsDto extends CacheableCollectionItem
{

    public function __construct(string $key, array $value) {
        parent::__construct($key, $value);
    }

    /**
     * Adds provided subdomain to the subdomains array if it's not there already.
     *
     * @param string $subdomain
     */
    public function add(string $subdomain): void
    {
        if (!in_array($subdomain, $this->value)) {
            $this->value[] = $subdomain;
        }
    }

    /**
     * @inheritDoc
     */
    public function getCacheValue(): string
    {
        $decoded = json_encode($this->value, JSON_UNESCAPED_SLASHES);
        return $decoded === false ? '' : $decoded;
    }
}
