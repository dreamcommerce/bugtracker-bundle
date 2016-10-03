<?php

namespace DreamCommerce\BugTrackerBundle\DependencyInjection;

use DreamCommerce\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class DreamCommerceBugTrackerExtension extends Extension
{
    const ALIAS = 'dream_commerce_bug_tracker';

    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
        $container->addCompilerPass(new CollectorCompilerPass());

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');
    }
}
