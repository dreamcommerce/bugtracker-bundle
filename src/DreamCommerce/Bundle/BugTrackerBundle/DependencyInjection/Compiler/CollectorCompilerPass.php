<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Collector\BaseCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\JiraCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\Psr3CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\QueueCollectorInterface;
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

        foreach ($container->getParameter(DreamCommerceBugTrackerExtension::ALIAS.'.collectors') as $name => $collector) {
            $interfaces = \class_implements($collector['class']);
            Assert::oneOf(CollectorInterface::class, $interfaces);

            $id = DreamCommerceBugTrackerExtension::ALIAS.'.collector.'.$name;

            $collectorDefinition = new Definition($collector['class']);
            $container->setDefinition($id, $collectorDefinition);

            if ($collector['type'] == BugHandler::COLLECTOR_TYPE_BASE) {
                Assert::oneOf(BaseCollectorInterface::class, $interfaces);
            } elseif ($collector['type'] == BugHandler::COLLECTOR_TYPE_PSR3) {
                Assert::oneOf(Psr3CollectorInterface::class, $interfaces);

                $serviceName = 'logger';
                if (isset($collector['logger'])) {
                    if (!empty($collector['logger'])) {
                        $serviceName = ltrim($collector['logger'], '@');
                    }
                    unset($collector['logger']);
                }

                $collectorDefinition->addArgument($container->getDefinition($serviceName));
            } elseif ($collector['type'] == BugHandler::COLLECTOR_TYPE_JIRA) {
                Assert::oneOf(JiraCollectorInterface::class, $interfaces);

                $serviceName = DreamCommerceBugTrackerExtension::ALIAS.'.http_client';
                if (isset($collector['http_client'])) {
                    if (!empty($collector['http_client'])) {
                        $serviceName = ltrim($collector['http_client'], '@');
                    }
                    unset($collector['http_client']);
                }

                $collectorDefinition->addArgument($container->getDefinition($serviceName));
            }

            $definition->addMethodCall(
                'registerCollector',
                array(new Reference($id), $collector['level'], $collector['priority'])
            );

            unset($collector['level']);
            unset($collector['priority']);
            unset($collector['class']);

            $collectorDefinition->addArgument($collector);
        }

        $taggedServices = $container->findTaggedServiceIds(
            DreamCommerceBugTrackerExtension::ALIAS.'.collector'
        );
        $supportedLevels = BugHandler::getSupportedLogLevels();

        foreach ($taggedServices as $id => $attributes) {
            $level = LogLevel::WARNING;
            if (isset($attributes['level'])) {
                $level = (string) $attributes['level'];
                Assert::oneOf($level, $supportedLevels);
            }

            $priority = QueueCollectorInterface::PRIORITY_NORMAL;
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
