<?php

namespace App\Tests\Unit\Utils;

use ReflectionException;

class ReflectionUtils
{

    /**
     * Invokes protected/private method on passed object
     *
     * @param $object
     * @param $methodName
     * @param array $parameters
     * @return mixed
     * @throws ReflectionException
     */
    public static function invokeProtectedOrPrivateMethod($object, $methodName, array $parameters = array()): mixed
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
