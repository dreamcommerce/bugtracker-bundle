<?php

namespace DreamCommerce\BugTracker\Collector;

interface CollectorInterface
{
    /**
     * @param \Error|\Exception $exc
     * @param int $level
     * @param array $context
     * @return bool
     */
    public function hasSupportException($exc, $level, array $context = array());

    /**
     * @param \Error|\Exception $exc
     * @param int $level
     * @param array $context
     * @return bool
     */
    public function handle($exc, $level, array $context = array());

    /**
     * @return bool
     */
    public function isCollected();
}