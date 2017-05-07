<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Fixtures\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionChainInterface;
use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionQueueInterface;
use Psr\Log\LogLevel;
use Throwable;

class CustomTestCollector implements CollectorInterface
{
    public function hasSupportException(Throwable $exception, string $level = LogLevel::WARNING, array $context = array()): bool
    {
        return true;
    }

    public function handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
    {
        // empty
    }

    public function isCollected(): bool
    {
        return false;
    }

    public function setExtensionQueue(CollectorExtensionQueueInterface $extensionChain)
    {
        // empty
    }
}
