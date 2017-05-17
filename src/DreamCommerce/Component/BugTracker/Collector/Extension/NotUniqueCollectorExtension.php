<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;

use Exception;

class NotUniqueCollectorExtension extends Exception
{
    public function __construct(string $extensionName)
    {
        parent::__construct(
            sprintf("Extension called %s already is registered. Name must be unique", $extensionName)
        );
    }
}