<?php

namespace DreamCommerce\Component\BugTracker;

use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\Exception\InvalidArgumentException;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

class BugHandler extends ErrorHandler
{
    const COLLECTOR_TYPE_BASE = 'base';
    const COLLECTOR_TYPE_PSR3 = 'psr3';
    const COLLECTOR_TYPE_JIRA = 'jira';
    const COLLECTOR_TYPE_CUSTOM = 'custom';

    /**
     * @var bool
     */
    private static $_enabled = false;

    /**
     * @var CollectorInterface
     */
    private static $_collector;

    /**
     * @var array
     */
    private static $_logLevels;

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
     * @return array
     */
    public static function getSupportedCollectorTypes()
    {
        return array(
            static::COLLECTOR_TYPE_BASE,
            static::COLLECTOR_TYPE_PSR3,
            static::COLLECTOR_TYPE_JIRA,
            static::COLLECTOR_TYPE_CUSTOM,
        );
    }

    public static function getSupportedLogLevels()
    {
        return array(
            LogLevel::DEBUG,
            LogLevel::INFO,
            LogLevel::NOTICE,
            LogLevel::WARNING,
            LogLevel::ERROR,
            LogLevel::CRITICAL,
            LogLevel::ALERT,
            LogLevel::EMERGENCY,
        );
    }

    /**
     * @return array
     */
    public static function getLogLevelPriorities()
    {
        if (static::$_logLevels === null) {
            static::$_logLevels = array_flip(static::getSupportedLogLevels());
        }

        return static::$_logLevels;
    }

    /**
     * @param string $level
     *
     * @return int
     */
    public function getLogLevelPriority($level)
    {
        if (is_string($level)) {
            $level = strtolower($level);
            $prioLevels = $this->getLogLevelPriorities();
            if (!isset($prioLevels[$level])) {
                throw new InvalidArgumentException('Unknown log level "'.$level.'"');
            }
            $level = $prioLevels[$level];
        } else {
            throw new InvalidArgumentException('Unsupported type of variable (expected: string; got: '.gettype($level).')');
        }

        return (int) $level;
    }
}
