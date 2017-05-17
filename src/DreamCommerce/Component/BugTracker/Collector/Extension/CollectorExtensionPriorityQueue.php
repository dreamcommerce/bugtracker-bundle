<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;

use SplPriorityQueue;

class CollectorExtensionPriorityQueue extends SplPriorityQueue implements CollectorExtensionQueueInterface
{
    const   NAME_KEY = 'name',
            OBJ_KEY  = 'object'
    ;

    /**
     * {@inheritdoc}
     */
    public function insert($value, $priority)
    {
        if (!is_array($value) || !isset($value[self::NAME_KEY]) || !isset($value[self::OBJ_KEY])) {
            throw new \InvalidArgumentException(
                sprintf("%s::%s need %s and %s keys in value array that contains extension name and extension object",
                    self::class,
                    __METHOD__,
                    self::NAME_KEY,
                    self::OBJ_KEY
                )
            );
        }

        if (!is_object($value[self::OBJ_KEY]) || is_object($value[self::OBJ_KEY]) && !($value[self::OBJ_KEY] instanceof CollectorExtensionInterface)) {
            throw new InvalidCollectorExtensionTypeException($value[self::OBJ_KEY]);
        }

        if ($this->has($value[self::NAME_KEY])) {
            throw NotUniqueCollectorExtension::forExtension($value[self::NAME_KEY]);
        }

        parent::insert($value, $priority);
    }

    /**
     * Register new extension for bug tracker
     *
     * @param CollectorExtensionInterface $extension
     * @param int $priority
     */
    public function registerExtension(string $name, CollectorExtensionInterface $extension, int $priority = -1)
    {
        if ($priority < 0) {
            $priority = $this->getMaxPriority() + 1;
        }

        $this->remove($name);

        $this->insert([
            self::NAME_KEY => $name,
            self::OBJ_KEY  => $extension
        ], $priority);
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

        $tmpExtensions = [];
        $found = false;

        $this->setExtractFlags(self::EXTR_BOTH);
        foreach ($this as $extension) {
            if (isset($extension['data'][self::NAME_KEY]) && $extension['data'][self::NAME_KEY] == $name) {
                $found = true;
                continue;
            }
            $tmpExtensions[] = $extension;
        }

        foreach ($tmpExtensions as $extension) {
            $this->insert($extension['data'], $extension['priority']);
        }

        return $found;
    }


    /**
     * Check if extension is registered
     *
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        foreach (clone $this as $extension) {
            if (isset($extension[self::NAME_KEY]) && $extension[self::NAME_KEY] == $name) {
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
        $additionalContext = [];

        /** @var ContextCollectorExtensionInterface $extension */
        foreach (clone $this as $extension) {
            if (!isset($extension[self::OBJ_KEY])) {
                continue;
            }

            $extensionObj = $extension[self::OBJ_KEY];
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
}