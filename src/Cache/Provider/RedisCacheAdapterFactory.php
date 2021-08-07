<?php

namespace App\Cache\Provider;

use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Component\Cache\Adapter\TagAwareAdapterInterface;

class RedisCacheAdapterFactory
{

    /**
     * Creates tag-aware redis adapter.
     *
     * @param string $redisUrl
     * @return TagAwareAdapterInterface
     */
    public static function createConnection(string $redisUrl): TagAwareAdapterInterface
    {
        $client = RedisAdapter::createConnection($redisUrl);
        return new RedisTagAwareAdapter($client);
    }
}
