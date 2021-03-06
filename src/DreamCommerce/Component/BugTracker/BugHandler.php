<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker;

use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Webmozart\Assert\Assert;

class BugHandler extends ErrorHandler
{
    const COLLECTOR_TYPE_BASE = 'base';
    const COLLECTOR_TYPE_PSR3 = 'psr3';
    const COLLECTOR_TYPE_JIRA = 'jira';
    const COLLECTOR_TYPE_DOCTRINE = 'doctrine';
    const COLLECTOR_TYPE_SWIFTMAILER = 'swiftmailer';
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
    public function handleError($type, $message, $file, $line)
    {
        try {
            if (4 < $numArgs = func_num_args()) {
                $context = func_get_arg(4) ?: array();
                $backtrace = 5 < $numArgs ? func_get_arg(5) : null; // defined on HHVM
            } else {
                $context = array();
                $backtrace = null;
            }

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
            static::COLLECTOR_TYPE_DOCTRINE,
            static::COLLECTOR_TYPE_SWIFTMAILER,
            static::COLLECTOR_TYPE_CUSTOM,
        );
    }

    /**
     * @return array
     */
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
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    public static function getLogLevelPriority($level)
    {
        Assert::string($level);

        $level = strtolower($level);
        $prioLevels = self::getLogLevelPriorities();
        Assert::keyExists($prioLevels, $level);

        return (int) $prioLevels[$level];
    }
}
