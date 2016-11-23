<?php

namespace DreamCommerce\Component\BugTracker\Collector;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

interface SymfonyCollectorInterface extends CollectorInterface
{
    /**
     * @param ConsoleExceptionEvent $event
     */
    public function handleConsoleException(ConsoleExceptionEvent $event);

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function handleKernelException(GetResponseForExceptionEvent $event);
}
