<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;


Interface CollectorExtendable
{
    /**
     * @param CollectorExtensionQueueInterface $extensionChain
     * @return null
     */
    public function setExtensionQueue(CollectorExtensionQueueInterface $extensionChain = null);
}