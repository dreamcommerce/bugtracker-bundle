<?php
namespace DreamCommerce\Tests\BugTrackerBundle\DependencyInjection\Compiler;

use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\Compiler\CollectorExtensionCompilerPass;
use DreamCommerce\Bundle\BugTrackerBundle\DependencyInjection\DreamCommerceBugTrackerExtension;
use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionChain;
use DreamCommerce\Component\BugTracker\CollectorExtension\CollectorExtensionChainInterface;
use DreamCommerce\Component\BugTracker\CollectorExtension\ContextCollectorExtensionInterface;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CollectorExtensionCompilerPassTest extends AbstractCompilerPassTestCase
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

        $this->registerService(DreamCommerceBugTrackerExtension::ALIAS. '.collector_extension_chain', CollectorExtensionChain::class);
    }

    public function testCompiling() {
        $this->assertContainerBuilderHasService(DreamCommerceBugTrackerExtension::ALIAS . '.collector_extension_chain');
        $this->registerService('collector_extansion_example1', ContextCollectorExtensionInterface::class)
            ->addTag(CollectorExtensionChainInterface::TAG_NAME);
        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithTag('collector_extansion_example1', CollectorExtensionChainInterface::TAG_NAME);
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new CollectorExtensionCompilerPass());
    }
}
