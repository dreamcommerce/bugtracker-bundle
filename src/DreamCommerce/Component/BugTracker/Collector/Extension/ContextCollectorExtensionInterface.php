<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector\Extension;

interface ContextCollectorExtensionInterface extends CollectorExtensionInterface
{
    public function getAdditionalContext(\Throwable $throwable): array;
}
