<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;


interface ContextCollectorExtensionInterface extends CollectorExtensionInterface
{
    public function getAdditionalContext(\Throwable $throwable): array;
}