<?php

namespace DreamCommerce\BugTrackerBundle\DependencyInjection\Compiler;

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
        if (!$container->has('dream_commerce_bugtracker.collector_chain')) {
            return;
        }

        $definition = $container->findDefinition(
            'dream_commerce_bugtracker.collector_chain'
        );

        $taggedServices = $container->findTaggedServiceIds(
            'dream_commerce_bugtracker.collector'
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