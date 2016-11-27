<?php

namespace DreamCommerce\Component\BugTracker\Generator;

interface TokenGeneratorInterface
{
    /**
     * @param \Exception|\Throwable $exc
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    public function generate($exc, $level, array $context = array());
}
