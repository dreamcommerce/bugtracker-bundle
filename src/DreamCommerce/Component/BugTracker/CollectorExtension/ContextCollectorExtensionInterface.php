<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;


interface ContextCollectorExtensionInterface extends CollectorExtensionInterface
{
    public function getAdditionalContext(\Throwable $throwable): array;
}