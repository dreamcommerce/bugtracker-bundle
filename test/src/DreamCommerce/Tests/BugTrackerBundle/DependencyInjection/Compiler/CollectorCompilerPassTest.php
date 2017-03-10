<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Tests\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorCompilerPass;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Bundle\BugTrackerBundle\Handler\SymfonyHandler;
use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Collector\DoctrineCollector;
use DreamCommerce\Component\BugTracker\Collector\Psr3Collector;
use DreamCommerce\Component\BugTracker\Collector\QueueCollectorInterface;
use DreamCommerce\Component\BugTracker\Generator\ContextTokenGenerator;
use DreamCommerce\Component\BugTracker\Model\Error;
use DreamCommerce\Fixtures\BugTracker\Collector\CustomTestCollector;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Psr\Log\LogLevel;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CollectorCompilerPassTest extends AbstractCompilerPassTestCase
{
    /**
     * @var array
     */
    private $globalOptions;

    /**
     * @var array
     */
    private $psrOptions;

    public function setUp()
    {
        parent::setUp();

        $this->registerService(DreamCommerceBugTrackerExtension::ALIAS. '.collector_queue', QueueCollectorInterface::class);
        $this->registerService(DreamCommerceBugTrackerExtension::ALIAS . '.symfony_handler', SymfonyHandler::class);
        $this->registerService(DreamCommerceBugTrackerExtension::ALIAS . '.token_generator', ContextTokenGenerator::class);
        $this->setParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', array());

        if ($this->globalOptions === null) {
            $this->globalOptions = array(
                'token_generator' => 'dream_commerce_bug_tracker.token_generator',
                'use_token' => false,
                'exceptions' => array(),
                'ignore_exceptions' => array()
            );

            $this->psrOptions = array(
                'logger' => 'monolog.logger',
                'format_exception' => false
            );
        }
    }

    public function testSingleCollector()
    {
        $options = array_merge($this->globalOptions, $this->psrOptions);

        $collectors = array(
            'foo' => array(
                'type' => BugHandler::COLLECTOR_TYPE_PSR3,
                'class' => Psr3Collector::class,
                'level' => LogLevel::WARNING,
                'priority' => QueueCollectorInterface::PRIORITY_NORMAL,
                'options' => $options
            )
        );

        $this->setParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $collectors);
        $this->compile();

        $this->assertContainerBuilderHasService(DreamCommerceBugTrackerExtension::ALIAS . '.foo_collector');

        $collector = new Reference(DreamCommerceBugTrackerExtension::ALIAS . '.foo_collector');
        $collectorDefinition = $this->container->getDefinition(DreamCommerceBugTrackerExtension::ALIAS . '.foo_collector');
        $this->assertEquals($collectors['foo']['class'], $collectorDefinition->getClass());

        $logger = new Reference('logger');

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(DreamCommerceBugTrackerExtension::ALIAS . '.foo_collector', 'setLogger', array($logger));
        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            DreamCommerceBugTrackerExtension::ALIAS.'.collector_queue',
            'registerCollector',
            array(
                $collector,
                $collectors['foo']['level'],
                $collectors['foo']['priority']
            )
        );
    }

    public function testMulitpleCollectors()
    {
        $options = array_merge($this->globalOptions, $this->psrOptions);

        $collectors = array(
            'foo' => array(
                'type' => BugHandler::COLLECTOR_TYPE_PSR3,
                'class' => Psr3Collector::class,
                'level' => LogLevel::WARNING,
                'priority' => QueueCollectorInterface::PRIORITY_NORMAL,
                'options' => $options
            ),
            'bar' => array(
                'type' => BugHandler::COLLECTOR_TYPE_CUSTOM,
                'class' => CustomTestCollector::class,
                'level' => LogLevel::ERROR,
                'priority' => QueueCollectorInterface::PRIORITY_LOW,
                'options' => $options
            )
        );

        $this->setParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $collectors);
        $this->compile();

        $this->assertContainerBuilderHasService(DreamCommerceBugTrackerExtension::ALIAS . '.foo_collector');
        $this->assertContainerBuilderHasService(DreamCommerceBugTrackerExtension::ALIAS . '.bar_collector');
    }

    public function testDoctrineCollector()
    {
        $collectors = array(
            'foo' => array(
                'type' => BugHandler::COLLECTOR_TYPE_DOCTRINE,
                'class' => DoctrineCollector::class,
                'level' => LogLevel::WARNING,
                'priority' => QueueCollectorInterface::PRIORITY_NORMAL,
                'options' => array(
                    'object_manager' => 'doctrine.orm.entity_manager',
                    'model' => Error::class,
                    'use_token' => true,
                    'use_counter' => true,
                    'counter_max_value' => 1000
                )
            )
        );

        $this->setParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $collectors);
        $this->compile();

        $this->assertContainerBuilderHasService(DreamCommerceBugTrackerExtension::ALIAS . '.foo_collector');
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CollectorCompilerPass());
    }
}
