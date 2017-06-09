<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Component\BugTracker\Collector\Extension;

use DreamCommerce\Component\Common\Model\TypedSplPriorityQueue;
use SplPriorityQueue;

class CollectorExtensionPriorityQueue extends TypedSplPriorityQueue implements CollectorExtensionQueueInterface
{
    const   CLASS_KEY       = 'class',
            PRIORITY_KEY    = 'priority'
    ;

    /**
     * Define expected object type
     *
     * @var string
     */
    protected $expectedObjectType = CollectorExtensionInterface::class;

    /**
     * @var array
     */
    private $registeredExtensions = array();

    public function insert($object, $priority)
    {
        if (!$this->isUnique($object, $priority)) {
            throw NotUniqueCollectorExtension::forExtension(get_class($object));
        }

        parent::insert($object, $priority);
    }

    /**
     * Register new extension for bug tracker
     *
     * @param CollectorExtensionInterface $extension
     * @param int $priority
     */
    public function registerExtension(CollectorExtensionInterface $extension, int $priority = null)
    {
        if ($priority == null) {
            $priority = $this->getMaxPriority() + 1;
        }

        $name = get_class($extension);
        $this->remove($name);

        $this->registeredExtensions[$name] = $priority;
        $this->insert($extension, $priority);
    }

    /**
     * Remove extension called $name from queue and return operation status
     * @param $extensionClass
     * @return bool
     */
    public function remove(string $extensionClass): bool
    {
        if (!$this->has($extensionClass)) {
            return false;
        }

        unset($this->registeredExtensions[$extensionClass]);

        $tmpExtensions = array();

        $this->setExtractFlags(self::EXTR_BOTH);
        foreach ($this as $extension) {
            if (!is_object($extension['data']) ||
                (is_object($extension['data']) && get_class($extension['data']) === $extensionClass)
            ) {
                continue;
            }

            $tmpExtensions[] = $extension;
        }

        foreach ($tmpExtensions as $extension) {
            $this->insert($extension['data'], $extension['priority'][0]);
        }

        return true;
    }

    /**
     * Check if extension is registered
     *
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        foreach (clone $this as $item) {
            if (is_object($item) && get_class($item) === $name) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get additional context from all registred extensions that implements ContextCollectorExtensionInterface
     *
     * @param \Throwable $throwable
     * @return array array with addidional contexts
     */
    public function getAdditionalContext(\Throwable $throwable): array
    {
        $additionalContext = array();

        /** @var ContextCollectorExtensionInterface $extensionObj */
        foreach (clone $this as $extensionObj) {
            if (!($extensionObj instanceof ContextCollectorExtensionInterface)) {
                continue;
            }

            $additionalContext = array_merge($additionalContext, $extensionObj->getAdditionalContext($throwable));
        }

        return $additionalContext;
    }

    /**
     * Return max priority that is set
     *
     * @return int
     */
    private function getMaxPriority() : int
    {
        if ($this->isEmpty()) {
            return 0;
        }

        $queue = clone $this;
        $queue->setExtractFlags(SplPriorityQueue::EXTR_PRIORITY);
        $maxPriority = 0;
        foreach ($queue as $priority) {
            if ($priority[0] > $maxPriority) {
                $maxPriority = $priority[0];
            }
        }

        return $maxPriority;
    }

    /**
     * Test if given object is unique
     *
     * @param CollectorExtensionInterface $object
     * @param int $priority
     * @return bool
     */
    private function isUnique(CollectorExtensionInterface $object, int $priority): bool
    {
        $queue = clone $this;
        $queue->setExtractFlags(self::EXTR_BOTH);

        foreach ($queue as $item) {
            $queuedObj      = $item['data'];
            $queuedPriority = (int)$item['priority'][0];

            if (is_object($queuedObj) && get_class($queuedObj) === get_class($object) && $queuedPriority === $priority) {
                return false;
            }
        }
        return true;
    }
}
