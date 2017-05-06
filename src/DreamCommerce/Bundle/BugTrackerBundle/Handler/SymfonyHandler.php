<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\Handler;

use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionChainInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

final class SymfonyHandler
{
    /**
     * @var CollectorInterface
     */
    private $_collector;

    public function __construct(CollectorInterface $collector)
    {
        $this->_collector       = $collector;
    }

    /**
     * @return CollectorInterface
     */
    public function getCollector()
    {
        return $this->_collector;
    }

    /**
     * @param CollectorInterface $collector
     *
     * @return $this
     */
    public function setCollector(CollectorInterface $collector)
    {
        $this->_collector = $collector;

        return $this;
    }

    /**
     * @param ConsoleExceptionEvent $event
     */
    public function handleConsoleException(ConsoleExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->getCollector()->handle($exception);
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function handleKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->getCollector()->handle($exception);
    }
}
