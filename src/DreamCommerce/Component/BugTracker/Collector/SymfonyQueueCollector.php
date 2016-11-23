<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class SymfonyQueueCollector extends QueueCollector implements SymfonyCollectorInterface
{
    public function handleConsoleException(ConsoleExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->handle($exception);
    }

    public function handleKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $this->handle($exception);
    }
}