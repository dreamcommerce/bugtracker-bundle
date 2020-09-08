<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension;

use DreamCommerce\Component\BugTracker\Collector\Extension\ContextCollectorExtensionInterface;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class RequestStackBasedExtension implements ContextCollectorExtensionInterface
{
    /**
     * @var RequestStack
     */
    protected $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }
}
