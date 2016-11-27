<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Collector\BaseCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\DoctrineCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\JiraCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\Psr3CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\QueueCollectorInterface;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class CollectorCompilerPass implements CompilerPassInterface
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

            switch ($collector['type']) {
                case BugHandler::COLLECTOR_TYPE_BASE:
                    Assert::oneOf(BaseCollectorInterface::class, $interfaces);
                    break;

                case BugHandler::COLLECTOR_TYPE_PSR3:
                    Assert::oneOf(Psr3CollectorInterface::class, $interfaces);

                    $serviceName = 'logger';
                    if (isset($collector['logger'])) {
                        if (!empty($collector['logger'])) {
                            $serviceName = ltrim($collector['logger'], '@');
                        }
                        unset($collector['logger']);
                    }

                    $collectorDefinition->addMethodCall('setLogger', $container->getDefinition($serviceName));
                    break;

                case BugHandler::COLLECTOR_TYPE_JIRA:
                    Assert::oneOf(JiraCollectorInterface::class, $interfaces);

                    $serviceName = DreamCommerceBugTrackerExtension::ALIAS.'.jira_connector';
                    if (isset($collector['connector'])) {
                        if (!empty($collector['connector'])) {
                            $serviceName = ltrim($collector['connector'], '@');
                        }
                        unset($collector['connector']);
                    }

                    $collectorDefinition->addMethodCall('setConnector', $container->getDefinition($serviceName));
                    break;

                case BugHandler::COLLECTOR_TYPE_DOCTRINE:
                    Assert::oneOf(DoctrineCollectorInterface::class, $interfaces);

                    $serviceName = 'doctrine.entity_manager';
                    if (isset($collector['entity_manager'])) {
                        if (!empty($collector['entity_manager'])) {
                            $serviceName = ltrim($collector['entity_manager'], '@');
                        }
                        unset($collector['entity_manager']);
                    }

                    $collectorDefinition->addMethodCall('setEntityManager', $container->getDefinition($serviceName));
                    break;

                case BugHandler::COLLECTOR_TYPE_SWIFTMAILER:
                    // TODO
                    break;
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
