<?php

namespace DreamCommerce\BugTrackerBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dream_commerce_bug_tracker');



        $rootNode
            ->children()
                ->arrayNode('handlers')
                    ->prototype('array')
                        ->children()
                            ->scalarNode('type')->cannotBeEmpty()->end()
                            ->arrayNode('options')->end()
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }


}
