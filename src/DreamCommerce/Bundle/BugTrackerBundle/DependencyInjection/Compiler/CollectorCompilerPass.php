<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\Collector\BaseCollector;
use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\JiraCollector;
use DreamCommerce\Component\BugTracker\Collector\Psr3Collector;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

class CollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->findDefinition(
            DreamCommerceBugTrackerExtension::ALIAS.'.collector_queue'
        );

        $additionalConfig = $container->getParameter(DreamCommerceBugTrackerExtension::ALIAS.'.configuration');

        foreach ($container->getParameter(DreamCommerceBugTrackerExtension::ALIAS.'.collectors') as $name => $collector) {
            $interfaces = \class_implements($collector['class']);
            Assert::oneOf(CollectorInterface::class, $interfaces);

            $id = DreamCommerceBugTrackerExtension::ALIAS.'.collector.'.$name;

            $collectorDefinition = new Definition($collector['class']);
            $container->setDefinition($id, $collectorDefinition);

            $classes = \class_parents($collector['class']);
            $classes[] = $collector['class'];

            if (isset($additionalConfig['default']) && in_array(BaseCollector::class, $classes)) {
                $collectorDefinition->addMethodCall('setOptions', $additionalConfig['default']);
            } elseif (isset($additionalConfig['jira']) && in_array(JiraCollector::class, $classes)) {
                $collectorDefinition->addMethodCall('setOptions', $additionalConfig['jira']);
            } elseif (isset($additionalConfig['psr3']) && in_array(Psr3Collector::class, $classes)) {
                $collectorDefinition->addMethodCall('setOptions', $additionalConfig['psr3']);
            }

            if (isset($collector['options']) && !empty($collector['options'])) {
                $collectorDefinition->addMethodCall('setOptions', $additionalConfig['options']);
            }

            if (in_array(Psr3Collector::class, $classes)) {
                $collectorDefinition->addArgument($container->getDefinition('logger'));
            } elseif (in_array(JiraCollector::class, $classes)) {
                $collectorDefinition->addArgument($container->getDefinition(DreamCommerceBugTrackerExtension::ALIAS.'.http_client'));
            }

            $definition->addMethodCall(
                'registerCollector',
                array(new Reference($id), $collector['level'], $collector['priority'])
            );
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
