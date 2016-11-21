<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CollectorCompilerPass implements CompilerPassInterface
{
    /**
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        if (!$container->has(DreamCommerceBugTrackerExtension::ALIAS . '.collector_queue')) {
            return;
        }

        $container->setAlias('bughandler', DreamCommerceBugTrackerExtension::ALIAS . '.collector_queue');
        $definition = $container->findDefinition(
            DreamCommerceBugTrackerExtension::ALIAS . '.collector_queue'
        );

        $taggedServices = $container->findTaggedServiceIds(
            DreamCommerceBugTrackerExtension::ALIAS . '.collector'
        );
        foreach ($taggedServices as $id => $attributes) {
            $level = LogLevel::WARNING;
            if(isset($attributes['level'])) {
                $level = (int) $attributes['level'];
            }

            $priority = 0;
            if(isset($attributes['priority'])) {
                $priority = (int) $attributes['priority'];
            }

            $definition->addMethodCall(
                'registerCollector',
                array(new Reference($id), $level, $priority)
            );
        }
    }
}