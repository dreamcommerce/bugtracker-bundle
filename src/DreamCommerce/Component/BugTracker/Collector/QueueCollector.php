<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\BugHandler;
use InvalidArgumentException;
use Psr\Log\LogLevel;
use Throwable;
use Webmozart\Assert\Assert;

class QueueCollector extends BaseCollector implements QueueCollectorInterface
{
    /**
     * @var int
     */
    private $_collectorSerials = PHP_INT_MAX;

    /**
     * @var CollectorPriorityQueue
     */
    private $_collectorQueue;

    /**
     * {@inheritdoc}
     */
    public function registerCollector(CollectorInterface $collector, string $level = LogLevel::WARNING, int $priority = 0)
    {
        $level = strtolower($level);
        Assert::oneOf($level, BugHandler::getSupportedLogLevels());

        $priority = (int) $priority;

        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new CollectorPriorityQueue();
        }

        if (!is_array($priority)) {
            $priority = array($priority, $this->_collectorSerials--);
        }

        $this->_collectorQueue->insert(
            array(
                'collector' => $collector,
                'level' => $level,
            ),
            $priority
        );
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterCollector($collector)
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new CollectorPriorityQueue();
        } else {
            $this->_collectorQueue->remove($collector);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterAllCollectors()
    {
        $this->_collectorQueue = null;
    }

    /**
     * {@inheritdoc}
     */
    public function getCollectors($collector = null)
    {
        if ($collector === null) {
            return $this->_collectorQueue->toArray();
        }

        $result = array();
        foreach (clone $this->_collectorQueue as $data) {
            if (is_object($collector)) {
                if ($collector === $data['collector']) {
                    $result[] = $data;
                }
            } elseif (is_string($collector)) {
                if (get_class($data['collector']) == $collector) {
                    $result[] = $data;
                }
            } else {
                throw new InvalidArgumentException('Unsupported type of variable');
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new CollectorPriorityQueue();
        }
        $this->setIsCollected(false);

        if (count($this->_collectorQueue) === 0) {
            return;
        }

        foreach (clone $this->_collectorQueue as $data) {
            /** @var CollectorInterface $collector */
            $collector = $data['collector'];

            if (BugHandler::getLogLevelPriority($level) < BugHandler::getLogLevelPriority($data['level'])) {
                continue;
            }

            try {
                if (!$collector->hasSupportException($exception, $level, $context)) {
                    continue;
                }

                $result = $collector->handle($exception, $level, $context);
                if ($collector->isCollected()) {
                    $this->setIsCollected(true);
                }
                if ($result === true) {
                    break;
                }
            } catch (Throwable $exc) {
                static::unregisterCollector($collector);
                static::handle($exc, LogLevel::CRITICAL);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _hasSupportException(Throwable $exception, string $level = LogLevel::WARNING, array $context = array()): bool
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new CollectorPriorityQueue();
        }

        foreach (clone $this->_collectorQueue as $data) {
            /** @var CollectorInterface $collector */
            $collector = $data['collector'];
            if ($collector->hasSupportException($exception, $level, $context)) {
                return true;
            }
        }

        return false;
    }
}
