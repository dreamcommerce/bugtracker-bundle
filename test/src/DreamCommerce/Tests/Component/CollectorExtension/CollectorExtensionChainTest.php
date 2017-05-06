<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;


use PHPUnit\Framework\TestCase;

class CollectorExtensionChainTest extends TestCase
{
    public function testRegisteringExtensions()
    {
        $collectorExtensionChain = new CollectorExtensionChain();
        $this->assertEquals(0, $collectorExtensionChain->countRegisteredExtensions());

        $collectorExtensionChain->registerExtension('extension1', $this->getDummyForCollectorExtensionInterface());
        $this->assertEquals(1, $collectorExtensionChain->countRegisteredExtensions());

        $collectorExtensionChain->registerExtension('extension2', $this->getDummyForCollectorExtensionInterface());
        $this->assertEquals(2, $collectorExtensionChain->countRegisteredExtensions());

        $collectorExtensionChain->registerExtension('extension3', $this->getDummyForContextCollectorExtensionInterface());
        $this->assertEquals(3, $collectorExtensionChain->countRegisteredExtensions());

        $collectorExtensionChain->registerExtension('extension1', $this->getDummyForContextCollectorExtensionInterface());
        $this->assertEquals(3, $collectorExtensionChain->countRegisteredExtensions());
    }

    public function testAdditionalContext()
    {
        $collectorExtensionChain = new CollectorExtensionChain();
        $collectorExtensionChain->registerExtension('ext1', $this->getStubForContextCollectorExtensionInterface([
            'a' => 'a',
            'b' => 'b'
        ]));
        $this->assertEquals(['a' => 'a', 'b' => 'b'], $collectorExtensionChain->getAdditionalContext(new \Exception()));


        $collectorExtensionChain->registerExtension('ext2', $this->getStubForContextCollectorExtensionInterface([
            'c' => 'c',
            'a' => 'd'
        ]));
        $this->assertEquals(['b' => 'b', 'c' => 'c', 'a' => 'd'], $collectorExtensionChain->getAdditionalContext(new \Exception()));

        /** Ignore because stub implements only CollectorExtensionInterface */
        $collectorExtensionChain->registerExtension('ext3', $this->getStubForCollectorExtensionInterface([
            'e' => 'e',
            'f' => 'f'
        ]));
        $this->assertEquals(['b' => 'b', 'c' => 'c', 'a' => 'd'], $collectorExtensionChain->getAdditionalContext(new \Exception()));
    }

    public function getDummyForCollectorExtensionInterface()
    {
        return $this->getMockBuilder(CollectorExtensionInterface::class)
            ->getMock();
    }

    public function getDummyForContextCollectorExtensionInterface()
    {
        return $this->getMockBuilder(ContextCollectorExtensionInterface::class)
            ->getMock();
    }

    public function getStubForContextCollectorExtensionInterface(array $return)
    {
        $stub = $this->getMockBuilder(ContextCollectorExtensionInterface::class)
            ->setMethods(['getAdditionalContext'])
            ->getMock();
        
        $stub->method('getAdditionalContext')
            ->willReturn($return);

        return $stub;
    }

    public function getStubForCollectorExtensionInterface()
    {
        $stub = $this->getMockBuilder(CollectorExtensionInterface::class)
            ->setMethods(['getAdditionalContext'])
            ->getMock();

        $stub->method('getAdditionalContext')
            ->willReturn([
                'c' => 'c',
                'd' => 'd'
            ]);

        return $stub;
    }
}