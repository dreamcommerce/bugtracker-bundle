<?php
namespace DreamCommerce\Component\BugTracker\Collector\Extension;


use PHPUnit\Framework\TestCase;
use DreamCommerce\Component\BugTracker\Collector\Extension\ContextCollectorExtensionInterface;


class CollectorExtensionPriorityQueueTest extends TestCase
{
    /**
     * @var int
     */
    private static $unique = 0;

    public function testRegisteringExtensions()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $this->assertEquals(0, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension($this->getDummyForCollectorExtensionInterface('1'));
        $this->assertEquals(1, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension($this->getDummyForCollectorExtensionInterface('2'));
        $this->assertEquals(2, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension($this->getDummyForContextCollectorExtensionInterface('3'));
        $this->assertEquals(3, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension($this->getDummyForContextCollectorExtensionInterface('3'));
        $this->assertEquals(3, $collectorExtensionQueue->count());
    }

    public function testPriority()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface(['a']), 3);
        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface(['b']), 2);
        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface(['c']), 1);
        $this->assertEquals(['a', 'b', 'c'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));


        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface(['a']));
        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface(['b']));
        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface(['c']));
        $this->assertEquals(['c', 'b', 'a'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));
    }

    public function testRemovingExtensions()
    {
        $ext1 = $this->getStubForContextCollectorExtensionInterface([]);
        $ext2 = $this->getStubForContextCollectorExtensionInterface([]);
        $ext3 = $this->getStubForContextCollectorExtensionInterface([]);
        $ext4 = $this->getStubForContextCollectorExtensionInterface([]);

        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->registerExtension($ext1, 1);
        $collectorExtensionQueue->registerExtension($ext2, 4);
        $collectorExtensionQueue->registerExtension($ext3, 1);
        $collectorExtensionQueue->registerExtension($ext4);
        $this->assertEquals(4, $collectorExtensionQueue->count());

        $this->assertTrue($collectorExtensionQueue->remove(get_class($ext1)));
        $this->assertEquals(3, $collectorExtensionQueue->count());
        $this->assertFalse($collectorExtensionQueue->remove(get_class($ext1)));
        $this->assertEquals(3, $collectorExtensionQueue->count());
        $this->assertTrue($collectorExtensionQueue->remove(get_class($ext2)));
        $this->assertTrue($collectorExtensionQueue->remove(get_class($ext3)));
        $this->assertEquals(1, $collectorExtensionQueue->count());
        $this->assertTrue($collectorExtensionQueue->remove(get_class($ext4)));
        $this->assertEquals(0, $collectorExtensionQueue->count());
    }

    public function testInsert()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->insert($this->getStubForContextCollectorExtensionInterface([]), 1);

        $this->assertEquals(1, $collectorExtensionQueue->count());
    }

    /**
     * @dataProvider invalidDataPreparedForInsert
     * @expectedException \TypeError
     */
    public function testInsertThrowsTypeError($value)
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->insert($value, 1);
    }


    /**
     * @expectedException \DreamCommerce\Component\BugTracker\Collector\Extension\NotUniqueCollectorExtension
     */
    public function testInsertThrowsNotUniqueCollectorExtension()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->insert($this->getStubForContextCollectorExtensionInterface([], '1'), 1);
        $collectorExtensionQueue->insert($this->getStubForContextCollectorExtensionInterface([], '1'), 1);
    }

    public function testAdditionalContext()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface([
            'a' => 'a',
            'b' => 'b'
        ]), 3);
        $this->assertEquals(['a' => 'a', 'b' => 'b'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));

        $collectorExtensionQueue->registerExtension($this->getStubForContextCollectorExtensionInterface([
            'c' => 'c',
            'a' => 'd'
        ]), 1);

        $this->assertEquals(['b' => 'b', 'c' => 'c', 'a' => 'd'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));
        /** Ignore because stub implements only CollectorExtensionInterface */
        $collectorExtensionQueue->registerExtension($this->getStubForCollectorExtensionInterface());
        $this->assertEquals(['b' => 'b', 'c' => 'c', 'a' => 'd'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));

    }

    /****************
     * STUBS
     ***************/
    public function getDummyForCollectorExtensionInterface($name = null)
    {
        if ($name === null) {
            $name = ++self::$unique;
        }

        return $this->getMockBuilder(CollectorExtensionInterface::class)
            ->setMockClassName('ContextCollectorExtension_'.$name)
            ->getMock();
    }

    public function getDummyForContextCollectorExtensionInterface($name = null)
    {
        if ($name === null) {
            $name = ++self::$unique;
        }

        return $this->getMockBuilder(ContextCollectorExtensionInterface::class)
            ->setMockClassName('ContextCollectorExtension_'.$name)
            ->getMock();
    }



    public function getStubForContextCollectorExtensionInterface(array $return, string $name = null)
    {
        if ($name === null) {
            $name = ++self::$unique;
        }
        $className = 'ContextCollectorExtension'.$name;
        if (!class_exists($className)) {
            eval($this->getCollectorExtensionInterfaceClassDeclaration($className));
        }

        return new $className($return);
    }

    public function getStubForCollectorExtensionInterface($name=null)
    {
        if ($name === null) {
            $name = ++self::$unique;
        }

        $stub = $this->getMockBuilder(CollectorExtensionInterface::class)
            ->setMockClassName('ContextCollectorExtension_'.$name)
            ->setMethods(['getAdditionalContext'])
            ->getMock();

        $stub->method('getAdditionalContext')
            ->willReturn([
                'c' => 'c',
                'd' => 'd'
            ]);

        return $stub;
    }


    /***********************
     * DATA PROVIDERS
     ***********************/
    public function invalidDataPreparedForInsert()
    {
        return [
            [ 'A' ],
            [ new \stdClass() ],
            [ null ],
            [ [] ],
            [ 1 ]
        ];
    }

    private function getCollectorExtensionInterfaceClassDeclaration($className)
    {
        return 'class '.$className.' implements \DreamCommerce\Component\BugTracker\Collector\Extension\ContextCollectorExtensionInterface
            {
                private $return;
    
                public function __construct(array $return)
            {
                $this->return = $return;
            }
    
                public function getAdditionalContext(\Throwable $throwable): array
            {
                return $this->return;
            }
        }';
    }
}