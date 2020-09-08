<?php

/**
 * (c) 2017-2020 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class DoctrineConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('doctrine');

        $baseConfiguration = new BaseConfiguration();
        $baseConfiguration->injectPartialNode($rootNode);

        $this->injectPartialNode($rootNode);

        return $treeBuilder;
    }

    public function injectPartialNode(ArrayNodeDefinition $node)
    {
        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('object_manager')->defaultValue('doctrine.orm.entity_manager')->cannotBeEmpty()->end()
                ->scalarNode('model')->cannotBeEmpty()->end()
                ->booleanNode('use_token')->defaultTrue()->end()
                ->booleanNode('use_counter')->defaultTrue()->end()
                ->integerNode('counter_max_value')->defaultValue(1000)->end()
            ->end();
    }
}
