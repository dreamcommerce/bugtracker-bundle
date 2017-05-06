<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;


interface CollectorExtensionChainInterface
{
    public function registerExtension(string $name, CollectorExtensionInterface $extension);

    public function getAdditionalContext(\Throwable $throwable): array;
}