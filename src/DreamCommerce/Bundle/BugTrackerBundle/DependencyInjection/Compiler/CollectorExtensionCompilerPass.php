<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtensionChainInterface;
use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtensionQueueInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
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
            $priority = (isset($tags[0]['priority'])) ? (int)$tags[0]['priority'] : CollectorExtensionQueueInterface::DEFAULT_PRIORITY;
            $chainDefinition->addMethodCall('registerExtension',  array(
                new Reference($id),
                $priority
            ));
        }
    }
}
