<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;

use Exception;

class InvalidCollectorExtensionTypeException extends Exception
{
    public function __construct($givenObject, string $expectedObject=null)
    {
        if (empty($expectedObject)) {
            $expectedObject = CollectorExtensionInterface::class;
        }

        if (is_object($givenObject)) {
            $type = get_class($givenObject);
        } else {
            $type = gettype($givenObject);
        }

        parent::__construct(sprintf("Invalid DreamCommerce bug tracker extension. Expected %s, given %s", $expectedObject, $type));
    }
}