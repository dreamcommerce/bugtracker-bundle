<?php

namespace DreamCommerce\BugTracker;

use DreamCommerce\BugTracker\Collector\CollectorInterface;
use DreamCommerce\BugTracker\Exception\ContextInterface;
use DreamCommerce\BugTracker\Exception\RuntimeException;
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
     * @var array
     */
    private static $_logLevels;

    /**
     * Enables the debug tools.
     *
     * This method registers an error handler and an exception handler.
     *
     * If the Symfony ClassLoader component is available, a special
     * class loader is also registered.
     *
     * @param int $errorReportingLevel The level of error reporting you want
     * @param bool $displayErrors Whether to display errors (for development) or just log them (for production)
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
     * @param \Exception|\Throwable $exc
     * @return array
     */
    public static function getContext($exc)
    {
        if(!is_object($exc)) {
            throw new RuntimeException('Unsupported type of variable (expected: object; got: ' . gettype($exc) . ')');
        }

        if(!($exc instanceof \Exception) && !($exc instanceof \Throwable)) {
            throw new RuntimeException('Unsupported class of object (expected: \Exception or \Throwable; got: ' . get_class($exc) . ')');
        }

        $context = array();
        if($exc instanceof ContextInterface) {
            $context = $exc->getContext();
        }

        return array_merge(
            $context,
            array(
                'message' => $exc->getMessage(),
                'code' => $exc->getCode(),
                'line' => $exc->getLine(),
                'file' => $exc->getFile()
            )
        );
    }

    /**
     * @return array
     */
    public static function getLogLevelPriorities()
    {
        if(static::$_logLevels === null) {
            static::$_logLevels = array_flip(
                array(
                    LogLevel::DEBUG,
                    LogLevel::INFO,
                    LogLevel::NOTICE,
                    LogLevel::WARNING,
                    LogLevel::ERROR,
                    LogLevel::CRITICAL,
                    LogLevel::ALERT,
                    LogLevel::EMERGENCY
                )
            );
        }

        return static::$_logLevels;
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
     * @param string $level
     * @return int
     */
    public static function getLogLevelPriority($level)
    {
        if(is_string($level)) {
            $level = strtolower($level);
            $prioLevels = static::getLogLevelPriorities();
            if (!isset($prioLevels[$level])) {
                throw new RuntimeException('Unknown log level "' . $level . '"');
            }
            $level = $prioLevels[$level];
        } else {
            throw new RuntimeException('Unsupported type of variable (expected: string; got: ' . gettype($level) . ')');
        }

        return (int)$level;
    }

    /**
     * {@inheritdoc}
     */
    public function handleException($exception, array $error = null)
    {
        if(static::$_collector === null) {
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
        }
    }
}