<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CollectorExtensionCompilerPass implements CompilerPassInterface
{
    const CHAIN_DEFINITION_NAME = 'dream_commerce_bug_tracker.collector_extension_chain';
    const EXTENSION_TAG = 'dream_commerce_bug_tracker.collector_extension';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::CHAIN_DEFINITION_NAME)) {
            return;
        }

        $chainDefinition = $container->getDefinition(self::CHAIN_DEFINITION_NAME);
        $taggedServices = $container->findTaggedServiceIds(self::EXTENSION_TAG);

        foreach ($taggedServices as $id => $tags) {
            $chainDefinition->addMethodCall('registerExtension', [
                $id,
                new Reference($id)
            ]);
        }
    }
}