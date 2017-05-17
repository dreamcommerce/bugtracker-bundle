<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtensionQueueInterface;
use InvalidArgumentException;
use Psr\Log\LogLevel;
use Throwable;

interface CollectorInterface
{
    /**
     * @param Throwable $exception
     * @param string                $level
     * @param array                 $context
     *
     * @throws InvalidArgumentException
     *
     * @return bool
     */
    public function hasSupportException(Throwable $exception, string $level = LogLevel::ERROR, array $context = array()): bool;

    /**
     * @param Throwable $exception
     * @param string                $level
     * @param array                 $context
     *
     * @throws InvalidArgumentException
     */
    public function handle(Throwable $exception, string $level = LogLevel::ERROR, array $context = array());

    /**
     * @param CollectorExtensionQueueInterface $extensionChain
     * @return null
     */
    public function setExtensionQueue(CollectorExtensionQueueInterface $extensionChain);

    /**
     * @return bool
     */
    public function isCollected(): bool;
}
