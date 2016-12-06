<?php

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
     *
     * @return bool
     */
    public function handle($exception, $level = LogLevel::WARNING, array $context = array());

    /**
     * @return bool
     */
    public function isCollected();
}
