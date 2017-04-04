<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Generator;

use Throwable;

interface TokenGeneratorInterface
{
    /**
     * @param Throwable $exception
     * @param int                   $level
     * @param array                 $context
     *
     * @return string
     */
    public function generate(Throwable $exception, string $level, array $context = array()): string;
}
