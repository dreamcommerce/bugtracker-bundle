<?php

namespace DreamCommerce\BugTracker;

use DreamCommerce\BugTracker\Collector\CollectorInterface;
use DreamCommerce\BugTracker\Collector\CollectorQueue;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\BufferingLogger;
use Symfony\Component\Debug\DebugClassLoader;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;

class BugHandler
{
    const PRIORITY_LOW = -512;
    const PRIORITY_NORMAL = 0;
    const PRIORITY_HIGH = 512;

    /**
     * @var int
     */
    private static $_collectorSerials = PHP_INT_MAX;

    /**
     * @var CollectorQueue
     */
    private static $_collectorQueue;

    /**
     * @var bool
     */
    private static $_enabled = false;

    /**
     * Enables the debug tools.
     *
     * This method registers an error handler and an exception handler.
     *
     * If the Symfony ClassLoader component is available, a special
     * class loader is also registered.
     *
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
            ErrorHandler::register(new ErrorHandler(new BufferingLogger()));
        } else {
            ErrorHandler::register()->throwAt(0, true);
        }

        DebugClassLoader::enable();
    }

    /**
     * @return bool
     */
    public static function isEnabled()
    {
        return static::$_enabled;
    }

    /**
     * @param CollectorInterface $collector
     * @param string $level
     * @param int $priority
     */
    public static function registerCollector(CollectorInterface $collector, $level = LogLevel::WARNING, $priority = 0)
    {
        if (static::$_collectorQueue === null) {
            static::$_collectorQueue = new CollectorQueue();
        }
        if (!is_array($priority)) {
            $priority = array($priority, static::$_collectorSerials--);
        }

        static::$_collectorQueue->insert(
            array(
                'collector' => $collector,
                'level' => $level
            ),
        $priority);
    }

    /**
     * @param string|CollectorInterface $collector
     * @throws \Exception
     */
    public static function unregisterCollector($collector)
    {
        if (static::$_collectorQueue === null) {
            static::$_collectorQueue = new CollectorQueue();
        } else {
            static::$_collectorQueue->remove($collector);
        }
    }

    public static function unregisterAllCollectors()
    {
        static::$_collectorQueue = null;
    }

    /**
     * @param string|CollectorInterface|null $collector
     * @return array
     * @throws \Exception
     */
    public static function getCollectors($collector = null)
    {
        if($collector === null) {
            return static::$_collectorQueue->toArray();
        }

        $result = array();
        foreach(clone static::$_collectorQueue as $data) {
            if(is_object($collector)) {
                if($collector === $data['collector']) {
                    $result[] = $data;
                }
            } elseif(is_string($collector)) {
                if(get_class($data['collector']) == $collector) {
                    $result[] = $data;
                }
            } else {
                throw new \RuntimeException('Unsupported type of variable');
            }
        }

        return $result;
    }

    /**
     * @param \Exception|\Throwable $exc
     * @param string $level
     * @param array $context
     * @return bool
     */
    public static function handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if(static::$_collectorQueue === null || count(static::$_collectorQueue) === 0) {
            return false;
        }

        $isCollected = false;
        foreach(clone static::$_collectorQueue as $data) {
            if($data['level'] < $level) {
                continue;
            }

            /** @var CollectorInterface $collector */
            $collector = $data['collector'];
            if(!$collector->hasSupportException($exc, $level)) {
                continue;
            }

            try {
                $result = $collector->handle($exc, $level, $context);
                if($isCollected === false && $collector->isCollected()) {
                    $isCollected = true;
                }
                if($result === true) {
                    break;
                }
            } catch(\Exception $exc) {
                static::unregisterCollector($collector);
                static::handle($exc);
            } catch (\Throwable $exc) {
                static::unregisterCollector($collector);
                static::handle($exc);
            }
        }

        return $isCollected;
    }
}