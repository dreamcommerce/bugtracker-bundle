<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\BaseConfiguration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\DoctrineConfiguration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\GlobalConfiguration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\JiraConfiguration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\Psr3Configuration;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Configuration\SwiftMailerConfiguration;
use DreamCommerce\Component\BugTracker\BugHandler;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class DreamCommerceBugTrackerExtension extends Extension
{
    const ALIAS = 'dream_commerce_bug_tracker';

    private $additionalConfigLoaded = array();

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
        $doctrineConfiguration = new DoctrineConfiguration();
        $swiftMailerConfiguration = new SwiftMailerConfiguration();

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/services'));
        $loader->load('base.xml');

        $globalBaseOptions = array();
        if(isset($config['configuration'][BugHandler::COLLECTOR_TYPE_BASE])) {
            $globalBaseOptions = $config['configuration'][BugHandler::COLLECTOR_TYPE_BASE];
        }

        foreach ($config['collectors'] as $name => $collectorConfig) {
            $globalPartialOptions = array();
            if (isset($config['configuration'][$collectorConfig['type']])) {
                $globalPartialOptions = $config['configuration'][$collectorConfig['type']];
            }

            $partialConfiguration = null;
            switch ($collectorConfig['type']) {
                case BugHandler::COLLECTOR_TYPE_BASE:
                    $partialConfiguration = $baseConfiguration;
                    break;
                case BugHandler::COLLECTOR_TYPE_PSR3:
                    $partialConfiguration = $psr3Configuration;
                    break;
                case BugHandler::COLLECTOR_TYPE_JIRA:
                    $partialConfiguration = $jiraConfiguration;
                    break;
                case BugHandler::COLLECTOR_TYPE_DOCTRINE:
                    $partialConfiguration = $doctrineConfiguration;
                    break;
                case BugHandler::COLLECTOR_TYPE_SWIFTMAILER:
                    $partialConfiguration = $swiftMailerConfiguration;
                    break;
            }
            $partialOptions = array();
            if($partialConfiguration !== null) {
                $partialOptions = $this->processConfiguration($partialConfiguration, array($collectorConfig['options']));
            }

            $config['collectors'][$name]['options'] = array_merge($globalBaseOptions, $globalPartialOptions, $partialOptions);
            $this->loadAdditionalConfiguration($container, $collectorConfig['type']);
        }

        $container->setParameter($this->getAlias().'.collectors', $config['collectors']);
        $container->addCompilerPass(new CollectorCompilerPass());
    }

    public function getAlias()
    {
        return self::ALIAS;
    }

    private function loadAdditionalConfiguration(ContainerBuilder $container, $type)
    {
        if (!in_array($type, $this->additionalConfigLoaded)) {
            $dirName = __DIR__.'/../Resources/config/services';
            $fileName = basename($type).'.xml';

            if (file_exists($dirName.'/'.$fileName)) {
                $loader = new Loader\XmlFileLoader($container, new FileLocator($dirName));
                $loader->load($fileName);
            }

            $this->additionalConfigLoaded[] = $type;
        }
    }
}
