<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;

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