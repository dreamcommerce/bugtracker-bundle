<?php
namespace DreamCommerce\Tests\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorExtensionCompilerPass;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtensionPriorityQueue;
use DreamCommerce\Component\BugTracker\Collector\Extension\CollectorExtensionQueueInterface;
use DreamCommerce\Component\BugTracker\Collector\Extension\ContextCollectorExtensionInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CollectorExtensionCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function setUp()
    {
        parent::setUp();

        $this->registerService(DreamCommerceBugTrackerExtension::ALIAS. '.collector_extension_chain', CollectorExtensionPriorityQueue::class);
    }

    public function testCompiling() {
        $this->assertContainerBuilderHasService(DreamCommerceBugTrackerExtension::ALIAS . '.collector_extension_chain');
        $this->registerService('collector_extansion_example1', ContextCollectorExtensionInterface::class)
            ->addTag(CollectorExtensionQueueInterface::TAG_NAME);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag('collector_extansion_example1', CollectorExtensionQueueInterface::TAG_NAME);
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CollectorExtensionCompilerPass());
    }
}
