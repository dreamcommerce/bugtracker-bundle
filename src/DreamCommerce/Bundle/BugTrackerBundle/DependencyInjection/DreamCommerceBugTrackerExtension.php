<?php

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\BaseConfiguration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\GlobalConfiguration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\JiraConfiguration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\Psr3Configuration;
use DreamCommerce\Component\BugTracker\BugHandler;
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
        $configuration = new GlobalConfiguration();
        $config = $this->processConfiguration($configuration, $configs);

        $baseConfiguration = new BaseConfiguration();
        $jiraConfiguration = new JiraConfiguration();
        $psr3Configuration = new Psr3Configuration();

        $useHttpServices = false;

        foreach($config['collectors'] as $name => $collectorConfig) {
            if(isset($collectorConfig['options'])) {
                $partialConfiguration = null;

                switch($collectorConfig['type']) {
                    case BugHandler::COLLECTOR_TYPE_BASE:
                        $partialConfiguration = $baseConfiguration;
                        break;
                    case BugHandler::COLLECTOR_TYPE_PSR3:
                        $partialConfiguration = $psr3Configuration;
                        break;
                    case BugHandler::COLLECTOR_TYPE_JIRA:
                        $partialConfiguration = $jiraConfiguration;
                        $useHttpServices = true;
                        break;
                    default:
                        continue;
                }

                $partialConfig = $collectorConfig['options'];
                if(isset($config['configuration'][$collectorConfig['type']])) {
                    $partialConfig = array_merge($config['configuration'][$collectorConfig['type']], $partialConfig);
                }

                $partialConfig = $this->processConfiguration($partialConfiguration, array($partialConfig));
                unset($collectorConfig['options']);
                $config['collectors'][$name] = array_merge($collectorConfig, $partialConfig);
            }
        }

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('base.xml');

        if($useHttpServices) {
            $loader->load('http.xml');
        }

        $container->setParameter($this->getAlias().'.collectors', $config['collectors']);
        $container->addCompilerPass(new CollectorCompilerPass());
    }

    public function getAlias()
    {
        return self::ALIAS;
    }
}
