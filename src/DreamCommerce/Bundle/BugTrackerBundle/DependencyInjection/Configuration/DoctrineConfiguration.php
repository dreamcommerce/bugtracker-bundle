<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration;

use DreamCommerce\Component\BugTracker\Model\Error;
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
                ->scalarNode('entity_manager')->defaultValue('doctrine.entity_manager')->cannotBeEmpty()->end()
                ->scalarNode('model')->defaultValue(Error::class)->cannotBeEmpty()->end()
            ->end();
    }
}
