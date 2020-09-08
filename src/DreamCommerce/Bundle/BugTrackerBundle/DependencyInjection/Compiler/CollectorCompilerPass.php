<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Collector\BaseCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\DoctrineCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\JiraCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\Psr3CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\QueueCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\SwiftMailerCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\TokenAwareInterface;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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

        $serviceResolver = function (&$collector, $serviceName, $configName = null) {
            if ($configName === null) {
                $configName = $serviceName;
            }

            if (isset($collector[$configName])) {
                if (!empty($collector[$configName])) {
                    $serviceName = ltrim($collector[$configName], '@');
                }
                unset($collector[$configName]);
            }

            return $serviceName;
        };

        foreach ($container->getParameter(DreamCommerceBugTrackerExtension::ALIAS.'.collectors') as $name => $collector) {
            $interfaces = \class_implements($collector['class']);
            Assert::oneOf(CollectorInterface::class, $interfaces);

            $id = DreamCommerceBugTrackerExtension::ALIAS.'.' . $name . '_collector';

            $collectorDefinition = new Definition($collector['class']);
            $container->setDefinition($id, $collectorDefinition);

            $skipTokenizer = false;
            switch ($collector['type']) {
                case BugHandler::COLLECTOR_TYPE_BASE:
                    Assert::oneOf(BaseCollectorInterface::class, $interfaces);
                    break;

                case BugHandler::COLLECTOR_TYPE_PSR3:
                    Assert::oneOf(Psr3CollectorInterface::class, $interfaces);

                    $serviceName = $serviceResolver($collector, 'logger');
                    $collectorDefinition->addMethodCall('setLogger', array(new Reference($serviceName)));
                    unset($collector['options']['logger']);

                    break;

                case BugHandler::COLLECTOR_TYPE_JIRA:
                    Assert::oneOf(JiraCollectorInterface::class, $interfaces);

                    $serviceName = $serviceResolver($collector, DreamCommerceBugTrackerExtension::ALIAS.'.jira_connector', 'connector');
                    $collectorDefinition->addMethodCall('setConnector', array(new Reference($serviceName)));
                    unset($collector['options']['connector']);

                    break;

                case BugHandler::COLLECTOR_TYPE_DOCTRINE:
                    Assert::oneOf(DoctrineCollectorInterface::class, $interfaces);

                    $serviceName = $serviceResolver($collector, 'doctrine.orm.entity_manager', 'object_manager');
                    $collectorDefinition->addMethodCall('setPersistManager', array(new Reference($serviceName)));
                    unset($collector['options']['object_manager']);

                    break;

                case BugHandler::COLLECTOR_TYPE_SWIFTMAILER:
                    Assert::oneOf(SwiftMailerCollectorInterface::class, $interfaces);

                    $serviceName = $serviceResolver($collector, 'mailer');
                    $collectorDefinition->addMethodCall('setMailer', array(new Reference($serviceName)));
                    unset($collector['options']['mailer']);

                    break;

                default:
                    $skipTokenizer = true;
            }

            if (!$skipTokenizer || in_array(TokenAwareInterface::class, $interfaces)) {
                $serviceName = $serviceResolver($collector, DreamCommerceBugTrackerExtension::ALIAS.'.token_generator', 'token_generator');
                $collectorDefinition->addMethodCall('setTokenGenerator', array(new Reference($serviceName)));
            }

            $definition->addMethodCall(
                'registerCollector',
                array(new Reference($id), $collector['level'], $collector['priority'])
            );

            unset($collector['level']);
            unset($collector['priority']);
            unset($collector['class']);
            unset($collector['options']['token_generator']);

            $collectorDefinition->addArgument($collector['options']);
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
