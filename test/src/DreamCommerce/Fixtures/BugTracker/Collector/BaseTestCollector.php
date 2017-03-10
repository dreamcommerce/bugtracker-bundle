<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Fixtures\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\Collector\BaseCollector;
use Psr\Log\LogLevel;
use Throwable;

class BaseTestCollector extends BaseCollector
{
    protected function _handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
    {
        // empty
    }
}
