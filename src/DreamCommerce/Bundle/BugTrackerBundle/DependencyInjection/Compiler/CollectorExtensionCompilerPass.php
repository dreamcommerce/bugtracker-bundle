<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CollectorExtensionCompilerPass implements CompilerPassInterface
{
    const CHAIN_DEFINITION_NAME = 'dream_commerce_bug_tracker.collector_extension_chain';
    const CONTEXT_EXTENSION_TAG = 'dream_commerce_bug_tracker.collector_extension_context';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::CHAIN_DEFINITION_NAME)) {
            return;
        }

        $definition = $container->getDefinition(self::CHAIN_DEFINITION_NAME);
        $taggedServices = $container->findTaggedServiceIds(self::CONTEXT_EXTENSION_TAG);

        foreach ($taggedServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $definition->addMethodCall('addTransport', array(
                    new Reference($id),
                    $attributes["alias"]
                ));
            }
        }
    }
}