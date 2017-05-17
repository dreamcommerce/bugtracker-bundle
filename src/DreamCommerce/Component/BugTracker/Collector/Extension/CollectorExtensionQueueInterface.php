<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;


interface CollectorExtensionQueueInterface extends \Countable
{
    const DEFAULT_PRIORITY = 0;

    const TAG_NAME = 'dream_commerce_bug_tracker.collector_extension';

    /**
     * Register new extension for bug tracker
     *
     * @param CollectorExtensionInterface $extension
     * @param int $priority
     */
    public function registerExtension(string $name, CollectorExtensionInterface $extension, int $priority=0);

    /**
     * Remove extension called $name from queue and return operation status
     * @param $name
     * @return bool
     */
    public function remove(string $name): bool;

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