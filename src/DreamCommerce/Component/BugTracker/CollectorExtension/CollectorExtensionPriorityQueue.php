<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;

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

        parent::insert($value, $priority);
    }

    /**
     * Register new extension for bug tracker
     *
     * @param CollectorExtensionInterface $extension
     * @param int $priority
     */
    public function registerExtension(string $name, CollectorExtensionInterface $extension, int $priority = 0)
    {
        $this->insert([
            self::NAME_KEY => $name,
            self::OBJ_KEY  => $extension
        ], $priority);
    }

    /**
     * Check if extension is registered
     *
     * @param $name
     * @return bool
     */
    public function has($name): bool
    {
        foreach ($this as $extension) {
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
        foreach ($this as $extension) {
            if (!($extension instanceof ContextCollectorExtensionInterface)) {
                continue;
            }

            $additionalContext = array_merge($additionalContext, $extension->getAdditionalContext($throwable));
        }

        return $additionalContext;
    }
}