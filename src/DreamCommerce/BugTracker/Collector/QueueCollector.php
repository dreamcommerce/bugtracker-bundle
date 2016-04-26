<?php

namespace DreamCommerce\BugTracker\Collector;

use DreamCommerce\BugTracker\BugHandler;
use DreamCommerce\BugTracker\Exception\RuntimeException;
use Psr\Log\LogLevel;

class QueueCollector implements CollectorInterface
{
    const PRIORITY_LOW = -100;
    const PRIORITY_NORMAL = 0;
    const PRIORITY_HIGH = 100;

    /**
     * @var int
     */
    private $_collectorSerials = PHP_INT_MAX;

    /**
     * @var SplPriorityQueue
     */
    private $_collectorQueue;

    /**
     * @var bool
     */
    protected $_isCollected = false;

    /**
     * @param CollectorInterface $collector
     * @param string $level
     * @param int $priority
     */
    public function registerCollector(CollectorInterface $collector, $level = LogLevel::WARNING, $priority = 0)
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
        }

        if (!is_array($priority)) {
            $priority = array($priority, $this->_collectorSerials--);
        }

        $this->_collectorQueue->insert(
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
    public function unregisterCollector($collector)
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
        } else {
            $this->_collectorQueue->remove($collector);
        }
    }

    /**
     * Remove all collectors from bugtracker
     */
    public function unregisterAllCollectors()
    {
        $this->_collectorQueue = null;
    }

    /**
     * @param string|CollectorInterface|null $collector
     * @return array
     * @throws \Exception
     */
    public function getCollectors($collector = null)
    {
        if ($collector === null) {
            return $this->toArray();
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
                throw new \RuntimeException('Unsupported type of variable');
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function handle($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if(!is_object($exc)) {
            throw new RuntimeException('Unsupported type of variable (expected: object; got: ' . gettype($exc) . ')');
        }

        if(!($exc instanceof \Exception) && !($exc instanceof \Throwable)) {
            throw new RuntimeException('Unsupported class of object (expected: \Exception|\Throwable; got: ' . get_class($exc) . ')');
        }

        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
        }
        $this->_isCollected = false;

        $levelPriority = BugHandler::getLogLevelPriority($level);
        if (count($this->_collectorQueue) === 0) {
            return false;
        }

        foreach (clone $this->_collectorQueue as $data) {
            $collectorLevelPriority = BugHandler::getLogLevelPriority($data['level']);
            if ($collectorLevelPriority > $levelPriority) {
                continue;
            }

            /** @var CollectorInterface $collector */
            $collector = $data['collector'];

            try {
                $result = $collector->handle($exc, $level, $context);
                if ($this->_isCollected === false && $collector->isCollected()) {
                    $this->_isCollected = true;
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
    public function hasSupportException($exc, $level = LogLevel::WARNING, array $context = array())
    {
        if ($this->_collectorQueue === null) {
            $this->_collectorQueue = new SplPriorityQueue();
        }

        $levelPriority = BugHandler::getLogLevelPriority($level);

        foreach (clone $this->_collectorQueue as $data) {
            $collectorLevelPriority = BugHandler::getLogLevelPriority($data['level']);
            if ($collectorLevelPriority > $levelPriority) {
                continue;
            }

            /** @var CollectorInterface $collector */
            $collector = $data['collector'];
            if ($collector->hasSupportException($exc, $level, $context)) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isCollected()
    {
        return $this->_isCollected;
    }
}