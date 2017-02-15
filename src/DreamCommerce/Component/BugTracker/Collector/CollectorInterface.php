<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use Psr\Log\LogLevel;

interface CollectorInterface
{
    /**
     * @param \Exception|\Throwable $exception
     * @param string                $level
     * @param array                 $context
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function hasSupportException($exception, $level = LogLevel::WARNING, array $context = array());

    /**
     * @param \Exception|\Throwable $exception
     * @param string                $level
     * @param array                 $context
     *
     * @throws \InvalidArgumentException
     */
    public function handle($exception, $level = LogLevel::WARNING, array $context = array());

    /**
     * @return bool
     */
    public function isCollected();
}
