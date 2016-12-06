<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Psr\Log\LogLevel;

interface QueueCollectorInterface
{
    const PRIORITY_LOW = -100;
    const PRIORITY_NORMAL = 0;
    const PRIORITY_HIGH = 100;

    /**
     * @param CollectorInterface $collector
     * @param string             $level
     * @param int                $priority
     *
     * @throws \InvalidArgumentException
     */
    public function registerCollector(CollectorInterface $collector, $level = LogLevel::WARNING, $priority = 0);

    /**
     * @param string|CollectorInterface $collector
     *
     * @throws \InvalidArgumentException
     */
    public function unregisterCollector($collector);

    /**
     * Remove all collectors from bugtracker.
     */
    public function unregisterAllCollectors();

    /**
     * @param string|CollectorInterface|null $collector
     *
     * @throws \InvalidArgumentException
     *
     * @return CollectorInterface[]
     */
    public function getCollectors($collector = null);
}
