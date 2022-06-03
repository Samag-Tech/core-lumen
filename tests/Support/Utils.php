<?php namespace Tests\Support;

use ReflectionMethod;
use ReflectionProperty;
use PHPUnit\Framework\MockObject\MockObject;

class Utils {

    public static function usePrivateMethod($object, $method, ...$args ) {

        if ( $object instanceof MockObject) {
            $obj = get_class($object);
        }
        else {
            $obj = $object;
        }


        $ref = new ReflectionMethod($obj, $method);
        $ref->setAccessible(true);

        return $ref->invoke($object, ...$args);
    }

    public static function usePrivateProperty($object, $property, $value = null) {

        if ( $object instanceof MockObject ) {
            $obj = get_class($object);
        }
        else {
            $obj = $object;
        }

        $ref = new ReflectionProperty($obj, $property);
        $ref->setAccessible(true);

        if ( ! is_null($value) ) {
            $ref->setValue($object, $value);
        }
        else {
            return $ref->getValue($object);
        }

    }
}