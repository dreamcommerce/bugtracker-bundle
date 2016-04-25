<?php

namespace DreamCommerce\BugTracker\Handler;

use DreamCommerce\BugTracker\BugHandler;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\ErrorHandler;

class HelperHandler extends ErrorHandler
{
    /**
     * {@inheritdoc}
     */
    public function handleException($exception, array $error = null)
    {
        BugHandler::handle($exception, LogLevel::ERROR);
    }
}