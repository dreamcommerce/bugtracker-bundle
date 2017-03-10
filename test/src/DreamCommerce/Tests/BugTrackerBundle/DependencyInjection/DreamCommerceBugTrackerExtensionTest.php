<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

namespace DreamCommerce\Tests\BugTrackerBundle\DependencyInjection;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\BugHandler;
use DreamCommerce\Component\BugTracker\Collector\Psr3Collector;
use DreamCommerce\Component\BugTracker\Collector\QueueCollectorInterface;
use DreamCommerce\Fixtures\BugTracker\Collector\BaseTestCollector;
use DreamCommerce\Fixtures\BugTracker\Collector\CustomTestCollector;
use DreamCommerce\Fixtures\BugTracker\Collector\Psr3TestColletor;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;
use Psr\Log\LogLevel;

class DreamCommerceBugTrackerExtensionTest extends AbstractExtensionTestCase
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

    public function testEmptyConfig()
    {
        $this->load();
        $this->assertContainerBuilderHasParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', array());
    }

    public function testSimplePsr3Collector()
    {
        $collectors = array(
            'foo' => array(
                'type' => BugHandler::COLLECTOR_TYPE_PSR3
            )
        );

        $this->load(array(
            'collectors' => $collectors
        ));

        $options = array_merge($this->globalOptions, $this->psrOptions);

        $expectedCollectors = array(
            'foo' => array(
                'type' => BugHandler::COLLECTOR_TYPE_PSR3,
                'class' => Psr3Collector::class,
                'level' => LogLevel::WARNING,
                'priority' => QueueCollectorInterface::PRIORITY_NORMAL,
                'options' => $options
            )
        );

        $this->assertContainerBuilderHasParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $expectedCollectors);
    }

    public function testExtendedPsr3Collector()
    {
        $options = array(
            'use_token' => true,
            'format_exception' => true
        );

        $collectors = array(
            'foo' => array(
                'class' => Psr3TestColletor::class,
                'type' => BugHandler::COLLECTOR_TYPE_PSR3,
                'level' => LogLevel::ERROR,
                'priority' => QueueCollectorInterface::PRIORITY_LOW,
                'options' => $options
            )
        );

        $this->load(array(
            'collectors' => $collectors
        ));

        $options = array_merge($this->globalOptions, $this->psrOptions, $options);
        $expectedCollectors = array(
            'foo' => array(
                'class' => Psr3TestColletor::class,
                'type' => BugHandler::COLLECTOR_TYPE_PSR3,
                'level' => LogLevel::ERROR,
                'priority' => QueueCollectorInterface::PRIORITY_LOW,
                'options' => $options
            )
        );

        $this->assertContainerBuilderHasParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $expectedCollectors);
    }

    public function testGlobalOptions()
    {
        $globalBaseOptions = array(
            'use_token' => true
        );

        $globalPsr3Options = array(
            'format_exception' => true
        );

        $psr3Options = array(
            'logger' => 'logger'
        );

        $collectors = array(
            'foo' => array(
                'class' => Psr3TestColletor::class,
                'type' => BugHandler::COLLECTOR_TYPE_PSR3,
                'level' => LogLevel::ERROR,
                'priority' => QueueCollectorInterface::PRIORITY_LOW,
                'options' => $psr3Options
            )
        );

        $this->load(array(
            'configuration' => array(
                'base' => $globalBaseOptions,
                'psr3' => $globalPsr3Options
            ),
            'collectors' => $collectors
        ));

        $options = array_merge($this->globalOptions, $this->psrOptions, $globalBaseOptions, $globalPsr3Options, $psr3Options);
        $expectedCollectors = array(
            'foo' => array(
                'class' => Psr3TestColletor::class,
                'type' => BugHandler::COLLECTOR_TYPE_PSR3,
                'level' => LogLevel::ERROR,
                'priority' => QueueCollectorInterface::PRIORITY_LOW,
                'options' => $options
            )
        );

        $this->assertContainerBuilderHasParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $expectedCollectors);
    }

    public function testCustomCollector()
    {
        $partialOptions = array(
            'foo' => 'bar',
            'bar' => 'baz'
        );

        $collectors = array(
            'foo' => array(
                'class' => CustomTestCollector::class,
                'type' => BugHandler::COLLECTOR_TYPE_CUSTOM,
                'options' => $partialOptions
            )
        );

        $this->load(array(
            'collectors' => $collectors
        ));

        $expectedCollectors = array(
            'foo' => array(
                'type' => BugHandler::COLLECTOR_TYPE_CUSTOM,
                'class' => CustomTestCollector::class,
                'level' => LogLevel::WARNING,
                'priority' => QueueCollectorInterface::PRIORITY_NORMAL,
                'options' => $partialOptions
            )
        );

        $this->assertContainerBuilderHasParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $expectedCollectors);
    }

    public function testBaseCollector()
    {
        $globalBaseOptions = array(
            'use_token' => true
        );

        $partialOptions = array(
            'exceptions' => array(
                \Exception::class
            )
        );


        $collectors = array(
            'foo' => array(
                'class' => BaseTestCollector::class,
                'options' => $partialOptions
            )
        );

        $this->load(array(
            'configuration' => array(
                'base' => $globalBaseOptions
            ),
            'collectors' => $collectors
        ));

        $options = array_merge($this->globalOptions, $globalBaseOptions, $partialOptions);

        $expectedCollectors = array(
            'foo' => array(
                'class' => BaseTestCollector::class,
                'level' => LogLevel::WARNING,
                'type' => BugHandler::COLLECTOR_TYPE_BASE,
                'priority' => QueueCollectorInterface::PRIORITY_NORMAL,
                'options' => $options
            )
        );

        $this->assertContainerBuilderHasParameter(DreamCommerceBugTrackerExtension::ALIAS . '.collectors', $expectedCollectors);
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerExtensions()
    {
        return array(
            new DreamCommerceBugTrackerExtension(),
        );
    }
}
