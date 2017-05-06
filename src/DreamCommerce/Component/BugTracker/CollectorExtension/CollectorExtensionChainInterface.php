<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;


interface CollectorExtensionChainInterface
{
    const TAG_NAME = 'dream_commerce_bug_tracker.collector_extension';


    public function registerExtension(string $name, CollectorExtensionInterface $extension);

    public function getAdditionalContext(\Throwable $throwable): array;
}