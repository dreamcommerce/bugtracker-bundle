<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector;

use DreamCommerce\Component\BugTracker\BugHandler;
use Psr\Log\LogLevel;
use Webmozart\Assert\Assert;

class QueueCollector extends BaseCollector implements QueueCollectorInterface
{
    /**
     * @var int
     */
    private $_collectorSerials = PHP_INT_MAX;

    /**
     * @var SplPriorityQueue
     */
    private $_collectorQueue;

    /**
     * {@inheritdoc}
     */
    public function registerCollector(CollectorInterface $collector, $level = LogLevel::WARNING, $priority = 0)
    {
        Assert::string($level);
        $level = strtolower($level);
        Assert::oneOf($level, BugHandler::getSupportedLogLevels());
        Assert::integerish($priority);

        $priority = (int) $priority;

        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
        }

        if (!is_array($priority)) {
            $priority = array($priority, $this->_collectorSerials--);
        }

        $this->_collectorQueue->insert(
            array(
                'collector' => $collector,
                'level' => $level,
            ),
            $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function unregisterCollector($collector)
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
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
                throw new \InvalidArgumentException('Unsupported type of variable');
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    protected function _handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
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
                if (!$collector->hasSupportException($exc, $level, $context)) {
                    continue;
                }

                $result = $collector->handle($exc, $level, $context);
                if ($collector->isCollected()) {
                    $this->setIsCollected(true);
                }
                if ($result === true) {
                    break;
                }
            } catch (\Exception $exc) {
                static::unregisterCollector($collector);
                static::handle($exc, LogLevel::CRITICAL);
            } catch (\Throwable $exc) {
                static::unregisterCollector($collector);
                static::handle($exc, LogLevel::CRITICAL);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _hasSupportException($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
        }

        foreach (clone $this->_collectorQueue as $data) {
            /** @var CollectorInterface $collector */
            $collector = $data['collector'];
            if ($collector->hasSupportException($exc, $level, $context)) {
                return true;
            }
        }

        return false;
    }
}
