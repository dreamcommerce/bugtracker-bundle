<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use InvalidArgumentException;
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
     * @throws InvalidArgumentException
     */
    public function registerCollector(CollectorInterface $collector, string $level = LogLevel::WARNING, int $priority = 0);

    /**
     * @param string|CollectorInterface $collector
     *
     * @throws InvalidArgumentException
     */
    public function unregisterCollector($collector);

    /**
     * Remove all collectors from bugtracker.
     */
    public function unregisterAllCollectors();

    /**
     * @param string|CollectorInterface|null $collector
     *
     * @throws InvalidArgumentException
     *
     * @return CollectorInterface[]
     */
    public function getCollectors($collector = null);
}
