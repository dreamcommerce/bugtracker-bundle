<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;

use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionInterface;

class InvalidExtensionTypeException extends \RuntimeException
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

        parent::__construct(sprintf("Invalid DreamCommerce bug tracker extension. Expected %s, Given %s", $expectedObject, $type));
    }
}