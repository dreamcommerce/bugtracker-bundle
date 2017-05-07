<?php
namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionChainInterface;
use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionQueueInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class CollectorExtensionCompilerPass implements CompilerPassInterface
{
    const CHAIN_DEFINITION_NAME = 'dream_commerce_bug_tracker.collector_extension_queue';

    public function process(ContainerBuilder $container)
    {
        if (!$container->hasDefinition(self::CHAIN_DEFINITION_NAME)) {
            return;
        }

        $chainDefinition = $container->getDefinition(self::CHAIN_DEFINITION_NAME);
        $taggedServices = $container->findTaggedServiceIds(CollectorExtensionQueueInterface::TAG_NAME);

        foreach ($taggedServices as $id => $tags) {
            $priority = (isset($tags[0]['priority'])) ? (int)$tags[0]['priority'] : -1;
            $chainDefinition->addMethodCall('registerExtension', [
                $id,
                new Reference($id),
                $priority
            ]);
        }
    }
}