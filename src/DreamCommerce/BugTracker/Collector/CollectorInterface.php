<?php

namespace DreamCommerce\BugTracker\Collector;

use Psr\Log\LogLevel;

interface CollectorInterface
{
    /**
     * @param \Error|\Exception $exc
     * @param string            $level
     * @param array             $context
     *
     * @return bool
     */
    public function hasSupportException($exc, $level = LogLevel::WARNING, array $context = array());

    /**
     * @param \Error|\Exception $exc
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
