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

final class SwiftMailerConfiguration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('swiftmailer');

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
                ->scalarNode('mailer')->defaultValue('mailer')->cannotBeEmpty()->end()
                ->scalarNode('subject')->defaultValue('An exception occurred')->cannotBeEmpty()->end()
                ->scalarNode('sender')->cannotBeEmpty()->end()
                ->arrayNode('recipients')
                    ->requiresAtLeastOneElement()
                    ->prototype('scalar')->end()
                ->end()
            ->end();
    }
}
