<?php

namespace DreamCommerce\Component\BugTracker;

use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use Webmozart\Assert\Assert as AssertBase;

/**
 * @method static void nullOrIsInstanceOneOf($value, array $classes = array(), $message = '')
 */
class Assert extends AssertBase
{
    public static function isInstanceOneOf($value, array $classes = array(), $message = '')
    {
        foreach ($classes as $class) {
            if ((class_exists($value) || interface_exists($value)) && $value instanceof $class) {
                return;
            }
        }

        static::reportInvalidArgument(sprintf(
            $message ?: 'Expected an instance of %2$s. Got: %s',
            static::typeToString($value),
            implode(', ', $classes)
        ));
    }

    protected static function reportInvalidArgument($message)
    {
        throw new InvalidArgumentException($message);
    }
}
