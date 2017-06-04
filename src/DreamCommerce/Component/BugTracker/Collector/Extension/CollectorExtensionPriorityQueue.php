<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;

use SplPriorityQueue;
use DreamCommerce\Component\Common\Model\TypedSplPriorityQueue;

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
    private $registeredExtensions = [];

    public function insert($object, $priority)
    {
        parent::insert($object, $priority);

        if (!$this->isUnique($object, $priority)) {
            throw NotUniqueCollectorExtension::forExtension(get_class($object));
        }
    }

    /**
     * Register new extension for bug tracker
     *
     * @param string $name
     * @param CollectorExtensionInterface $extension
     * @param int $priority
     */
    public function registerExtension(string $name, CollectorExtensionInterface $extension, int $priority = 0)
    {
        if ($priority < 0) {
            $priority = $this->getMaxPriority() + 1;
        }
        $this->remove($name);

        $this->registeredExtensions[$name] = [
            self::CLASS_KEY     => get_class($extension),
            self::PRIORITY_KEY  => $priority
        ];

        $this->insert($extension, $priority);
    }

    /**
     * Remove extension called $name from queue and return operation status
     * @param $name
     * @return bool
     */
    public function remove(string $name): bool
    {
        if (!$this->has($name)) {
            return false;
        }

        $removingExtension = $this->registeredExtensions[$name];
        unset($this->registeredExtensions[$name]);

        $tmpExtensions = [];

        $this->setExtractFlags(self::EXTR_BOTH);
        foreach ($this as $extension) {
            if (!is_object($extension['data'])) {
                continue;
            }
            if (get_class($extension['data']) !== $removingExtension[self::CLASS_KEY] ||
                $extension['priority'] !== $removingExtension[self::PRIORITY_KEY]
            ) {
                continue;
            }
            $tmpExtensions[] = $extension;
        }

        foreach ($tmpExtensions as $extension) {
            $this->insert($extension['data'], $extension['priority']);
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
        return (isset($this->registeredExtensions[$name]));
    }

    /**
     * Get additional context from all registred extensions that implements ContextCollectorExtensionInterface
     *
     * @param \Throwable $throwable
     * @return array array with addidional contexts
     */
    public function getAdditionalContext(\Throwable $throwable): array
    {
        $additionalContext = [];

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
            if ($priority > $maxPriority) {
                $maxPriority = $priority;
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

        foreach ($queue as $extension) {
            var_dump(get_class($object));
            var_dump(get_class($extension['data']));
            var_dump($priority);
            var_dump($extension['priority']);
            echo PHP_EOL;
            if (is_object($extension['data']) && get_class($extension['data']) === get_class($object) &&
                (int)$extension['priority'] === (int)$priority
            ) {
                return false;
            }
        }

        return true;
    }
}