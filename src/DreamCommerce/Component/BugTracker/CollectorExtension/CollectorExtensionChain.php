<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;

class CollectorExtensionChain implements CollectorExtensionChainInterface
{
    /**
     * Handle registered extensions
     *
     * @var array
     */
    protected $registeredExtensions = [];

    /**
     * Register an extension
     *
     * @param string $name
     * @param CollectorExtensionInterface $collectorExtension
     */
    public function registerExtension(string $name, CollectorExtensionInterface $collectorExtension)
    {
        $this->registeredExtensions[$name] = $collectorExtension;
    }


    /**
     * Collect additional context from all registered extension
     *
     * @param \Throwable $throwable
     * @return array
     */
    public function getAdditionalContext(\Throwable $throwable): array
    {
        $additionalContext = [];

        /** @var ContextCollectorExtensionInterface $extension */
        foreach ($this->registeredExtensions as $extension) {
            if (!($extension instanceof ContextCollectorExtensionInterface)) {
                continue;
            }

            $additionalContext = array_merge($additionalContext, $extension->getAdditionalContext($throwable));
        }

        return $additionalContext;
    }

    /**
     * Get information how much extensions is registered
     *
     * @return int
     */
    public function countRegisteredExtensions(): int
    {
        return count($this->registeredExtensions);
    }
}