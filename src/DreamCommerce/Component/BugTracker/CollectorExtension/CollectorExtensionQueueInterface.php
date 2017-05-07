<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;


interface CollectorExtensionQueueInterface extends \Countable
{
    const TAG_NAME = 'dream_commerce_bug_tracker.collector_extension';

    /**
     * Register new extension for bug tracker
     *
     * @param CollectorExtensionInterface $extension
     * @param int $priority
     */
    public function registerExtension(string $name, CollectorExtensionInterface $extension, int $priority=0);


    /**
     * Check if extension is registered
     *
     * @param $name
     * @return bool
     */
    public function has($name): bool;

    /**
     * Get additional context from all registred extensions that implements ContextCollectorExtensionInterface
     *
     * @param \Throwable $throwable
     * @return array array with addidional contexts
     */
    public function getAdditionalContext(\Throwable $throwable): array;
}