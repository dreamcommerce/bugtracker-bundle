<?php

namespace DreamCommerce\BugTrackerBundle\Collector;

use Psr\Log\LogLevel;

interface CollectorInterface
{
    /**
     * @param \Exception|\Throwable $exc
     * @param string            $level
     * @param array             $context
     *
     * @return bool
     */
    public function hasSupportException($exc, $level = LogLevel::WARNING, array $context = array());

    /**
     * @param \Exception|\Throwable $exc
     * @param string            $level
     * @param array             $context
     *
     * @return bool
     */
    public function handle($exc, $level = LogLevel::WARNING, array $context = array());

    /**
     * @return bool
     */
    public function isCollected();
}
