<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector\Extension;

interface CollectorExtendable
{
    /**
     * @param CollectorExtensionQueueInterface $extensionChain
     * @return null
     */
    public function setExtensionQueue(CollectorExtensionQueueInterface $extensionChain = null);
}
