<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\JiraCollector;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

class CollectorCompilerPass implements CompilerPassInterface
{
    private static $collectorNumber = 0;

    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(
            DreamCommerceBugTrackerExtension::ALIAS.'.collector_queue'
        );

        $additionalConfig = $container->getParameter(DreamCommerceBugTrackerExtension::ALIAS.'.configuration');

        foreach ($container->getParameter(DreamCommerceBugTrackerExtension::ALIAS.'.handlers') as $handler) {
            $interfaces = \class_implements($handler['class']);
            Assert::oneOf(CollectorInterface::class, $interfaces);

            $id = DreamCommerceBugTrackerExtension::ALIAS.'.handler'.self::$collectorNumber;

            $collectorDefinition = new Definition($handler['class']);
            $collectorDefinition->addArgument($handler['options']);

            $container->setDefinition($id, $collectorDefinition);

            if (isset($additionalConfig['jira'])) {
                $classes = \class_parents($handler['class']);
                if (in_array(JiraCollector::class, $classes)) {
                    $collectorDefinition->addMethodCall('setOptions', $additionalConfig['jira']);
                }
            }

            $definition->addMethodCall(
                'registerCollector',
                array(new Reference($id), $handler['level'], $handler['priority'])
            );

            ++self::$collectorNumber;
        }

        $taggedServices = $container->findTaggedServiceIds(
            DreamCommerceBugTrackerExtension::ALIAS.'.collector'
        );
        foreach ($taggedServices as $id => $attributes) {
            $level = LogLevel::WARNING;
            if (isset($attributes['level'])) {
                $level = (int) $attributes['level'];
            }

            $priority = 0;
            if (isset($attributes['priority'])) {
                $priority = (int) $attributes['priority'];
            }

            $definition->addMethodCall(
                'registerCollector',
                array(new Reference($id), $level, $priority)
            );
        }
    }
}
