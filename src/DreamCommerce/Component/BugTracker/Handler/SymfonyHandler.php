<?php

namespace DreamCommerce\Component\BugTracker\Handler;

use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
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
        $this->_collector = $collector;
    }

    public function handleConsoleException(ConsoleExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->_collector->handle($exception);
    }

    public function handleKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->_collector->handle($exception);
    }
}
