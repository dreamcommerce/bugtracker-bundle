<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
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
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        $container->setParameter($this->getAlias().'.collectors', $config['collectors']);
        $container->setParameter($this->getAlias().'.configuration', $config['configuration']);

        $container->addCompilerPass(new CollectorCompilerPass(), PassConfig::TYPE_REMOVE);
    }

    public function getAlias()
    {
        return self::ALIAS;
    }
}
