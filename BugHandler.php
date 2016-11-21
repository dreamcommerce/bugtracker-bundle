<?php

namespace DreamCommerce\BugTrackerBundle;

use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

class BugHandler extends ErrorHandler
{
    /**
     * @var bool
     */
    private static $_enabled = false;

    /**
     * @var CollectorInterface
     */
    private static $_collector;

    /**
     * @param int  $errorReportingLevel The level of error reporting you want
     * @param bool $displayErrors       Whether to display errors (for development) or just log them (for production)
     */
    public static function enable($errorReportingLevel = E_ALL, $displayErrors = true)
    {
        if (static::$_enabled) {
            return;
        }

        static::$_enabled = true;

        if (null !== $errorReportingLevel) {
            error_reporting($errorReportingLevel);
        } else {
            error_reporting(E_ALL);
        }

        if ('cli' !== PHP_SAPI) {
            ini_set('display_errors', 0);
            ExceptionHandler::register();
        } elseif ($displayErrors && (!ini_get('log_errors') || ini_get('error_log'))) {
            // CLI - display errors only if they're not already logged to STDERR
            ini_set('display_errors', 1);
        }
        if ($displayErrors) {
            ErrorHandler::register(new static(new BufferingLogger()));
        } else {
            ErrorHandler::register(new static())->throwAt(0, true);
        }

        DebugClassLoader::enable();
    }

    /**
     * @return CollectorInterface|null
     */
    public static function getCollector()
    {
        return self::$_collector;
    }

    /**
     * @param CollectorInterface $collector
     */
    public static function setCollector(CollectorInterface $collector)
    {
        self::$_collector = $collector;
    }

    /**
     * {@inheritdoc}
     */
    public function handleException($exception, array $error = null)
    {
        if (static::$_collector === null) {
            parent::handleException($exception, $error);
        } else {
            static::$_collector->handle($exception, LogLevel::ERROR);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function handleError($type, $message, $file, $line, array $context, array $backtrace = null)
    {
        try {
            parent::handleError($type, $message, $file, $line, $context, $backtrace);
        } catch (\Exception $ex) {
            $this->handleException($ex);
            throw $ex;
        } catch (\Throwable $ex) {
            $this->handleException($ex);
            throw $ex;
        }
    }
}
