<?php

/**
 * (c) 2017-2020 DreamCommerce
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
use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\DoctrineCollector;
use DreamCommerce\Component\BugTracker\Collector\DoctrineCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\JiraCollector;
use DreamCommerce\Component\BugTracker\Collector\JiraCollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\Psr3Collector;
use DreamCommerce\Component\BugTracker\Collector\Psr3CollectorInterface;
use DreamCommerce\Component\BugTracker\Collector\SwiftMailerCollector;
use DreamCommerce\Component\BugTracker\Collector\SwiftMailerCollectorInterface;
use RuntimeException;
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
        if (isset($config['configuration'][BugHandler::COLLECTOR_TYPE_BASE])) {
            $globalBaseOptions = $config['configuration'][BugHandler::COLLECTOR_TYPE_BASE];
        }

        foreach ($config['collectors'] as $name => $collectorConfig) {
            $globalPartialOptions = array();
            if (isset($config['configuration'][$collectorConfig['type']])) {
                $globalPartialOptions = $config['configuration'][$collectorConfig['type']];
            }

            $partialConfiguration = null;
            $partialClass = null;
            $partialInterface = null;

            switch ($collectorConfig['type']) {
                case BugHandler::COLLECTOR_TYPE_BASE:
                    $partialConfiguration = $baseConfiguration;
                    $partialInterface = CollectorInterface::class;
                    break;
                case BugHandler::COLLECTOR_TYPE_PSR3:
                    $partialConfiguration = $psr3Configuration;
                    $partialClass = Psr3Collector::class;
                    $partialInterface = Psr3CollectorInterface::class;
                    break;
                case BugHandler::COLLECTOR_TYPE_JIRA:
                    $partialConfiguration = $jiraConfiguration;
                    $partialClass = JiraCollector::class;
                    $partialInterface = JiraCollectorInterface::class;
                    break;
                case BugHandler::COLLECTOR_TYPE_DOCTRINE:
                    $partialConfiguration = $doctrineConfiguration;
                    $partialClass = DoctrineCollector::class;
                    $partialInterface = DoctrineCollectorInterface::class;
                    break;
                case BugHandler::COLLECTOR_TYPE_SWIFTMAILER:
                    $partialConfiguration = $swiftMailerConfiguration;
                    $partialClass = SwiftMailerCollector::class;
                    $partialInterface = SwiftMailerCollectorInterface::class;
                    break;
                case BugHandler::COLLECTOR_TYPE_CUSTOM:
                    $partialInterface = CollectorInterface::class;
                    break;
                default:
                    throw new RuntimeException('Type "' . $collectorConfig['type'] . '" is not supported');
            }

            if (empty($collectorConfig['class'])) {
                if ($partialClass === null) {
                    throw new RuntimeException('Parameter "class" is required');
                }
                $config['collectors'][$name]['class'] = $partialClass;
            } else {
                if (!class_exists($collectorConfig['class'])) {
                    throw new RuntimeException('Class "' . $collectorConfig['class'] . '" does not exist');
                }
                $interfaces = class_implements($collectorConfig['class']);
                if (!in_array($partialInterface, $interfaces)) {
                    throw new RuntimeException('Class "' . $collectorConfig['class'] . '" does not implement interface "' . $partialInterface . '"');
                }
            }

            if ($collectorConfig['type'] === BugHandler::COLLECTOR_TYPE_CUSTOM) {
                $partialOptions = $collectorConfig['options'];
            } else {
                $partialOptions = array_merge($globalBaseOptions, $globalPartialOptions, $collectorConfig['options']);
            }

            if ($partialConfiguration !== null) {
                $partialOptions = $this->processConfiguration($partialConfiguration, array($partialOptions));
            }

            $config['collectors'][$name]['options'] = $partialOptions;
            $this->loadAdditionalConfiguration($container, $collectorConfig['type']);
        }

        $container->setParameter($this->getAlias().'.collectors', $config['collectors']);
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
