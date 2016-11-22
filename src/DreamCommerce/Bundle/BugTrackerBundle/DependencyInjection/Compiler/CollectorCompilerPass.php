<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\Collector\BaseCollector;
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

        foreach ($container->getParameter(DreamCommerceBugTrackerExtension::ALIAS.'.handlers') as $collector) {
            $interfaces = \class_implements($collector['class']);
            Assert::oneOf(CollectorInterface::class, $interfaces);

            $id = DreamCommerceBugTrackerExtension::ALIAS.'.handler'.self::$collectorNumber;

            $collectorDefinition = new Definition($collector['class']);
            $container->setDefinition($id, $collectorDefinition);

            if (isset($additionalConfig['default'])) {
                $classes = \class_parents($collector['class']);
                if (in_array(BaseCollector::class, $classes)) {
                    $collectorDefinition->addMethodCall('setOptions', $additionalConfig['default']);
                }
            }

            if (isset($additionalConfig['jira'])) {
                $classes = \class_parents($collector['class']);
                if (in_array(JiraCollector::class, $classes)) {
                    $collectorDefinition->addMethodCall('setOptions', $additionalConfig['jira']);
                }
            }
            
            if(isset($collector['options']) && !empty($collector['options'])) {
                $collectorDefinition->addMethodCall('setOptions', $additionalConfig['options']);
            }

            $definition->addMethodCall(
                'registerCollector',
                array(new Reference($id), $collector['level'], $collector['priority'])
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
