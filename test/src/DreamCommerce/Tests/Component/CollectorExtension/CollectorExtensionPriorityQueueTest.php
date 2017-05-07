<?php
namespace DreamCommerce\Component\BugTracker\CollectorExtension;


use PHPUnit\Framework\TestCase;

class CollectorExtensionPriorityQueueTest extends TestCase
{
    public function testRegisteringExtensions()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $this->assertEquals(0, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension('extension1', $this->getDummyForCollectorExtensionInterface());
        $this->assertEquals(1, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension('extension2', $this->getDummyForCollectorExtensionInterface());
        $this->assertEquals(2, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension('extension3', $this->getDummyForContextCollectorExtensionInterface());
        $this->assertEquals(3, $collectorExtensionQueue->count());

        $collectorExtensionQueue->registerExtension('extension1', $this->getDummyForContextCollectorExtensionInterface());
        $this->assertEquals(3, $collectorExtensionQueue->count());
    }

    public function testRemovingExtensions()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->registerExtension('ext2', $this->getStubForContextCollectorExtensionInterface([]), 1);
        $collectorExtensionQueue->registerExtension('ext1', $this->getStubForContextCollectorExtensionInterface([]), 4);
        $collectorExtensionQueue->registerExtension('ext3', $this->getStubForContextCollectorExtensionInterface([]), 1);
        $collectorExtensionQueue->registerExtension('ext4', $this->getStubForContextCollectorExtensionInterface([]));
        $this->assertEquals(4, $collectorExtensionQueue->count());

        $this->assertTrue($collectorExtensionQueue->remove('ext1'));
        $this->assertEquals(3, $collectorExtensionQueue->count());
        $this->assertFalse($collectorExtensionQueue->remove('ext1'));
        $this->assertEquals(3, $collectorExtensionQueue->count());
        $this->assertTrue($collectorExtensionQueue->remove('ext2'));
        $this->assertTrue($collectorExtensionQueue->remove('ext3'));
        $this->assertEquals(1, $collectorExtensionQueue->count());
        $this->assertTrue($collectorExtensionQueue->remove('ext4'));
        $this->assertEquals(0, $collectorExtensionQueue->count());
    }

    public function testInsert()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->insert([
            CollectorExtensionPriorityQueue::NAME_KEY => 'a1',
            CollectorExtensionPriorityQueue::OBJ_KEY  => $this->getStubForContextCollectorExtensionInterface([])
        ], 1);

        $this->assertEquals(1, $collectorExtensionQueue->count());
    }

    /**
     * @dataProvider invalidDataPreparedForInsert
     * @expectedException \InvalidArgumentException
     */
    public function testInsertThrowsInvalidArgumentException($value)
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->insert($value, 1);
    }

    /**
     * @dataProvider invalidObjectDataForInsert
     * @expectedException \DreamCommerce\Component\BugTracker\CollectorExtension\InvalidCollectorExtensionTypeException
     */
    public function testInsertThrowsInvalidCollectorExtensionTypeException($name, $object)
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->insert([
            CollectorExtensionPriorityQueue::NAME_KEY => $name, 
            CollectorExtensionPriorityQueue::OBJ_KEY  => $object
        ], 1);
    }

    /**
     * @expectedException \DreamCommerce\Component\BugTracker\CollectorExtension\NotUniqueCollectorExtension
     */
    public function testInsertThrowsNotUniqueCollectorExtension()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->insert([
            CollectorExtensionPriorityQueue::NAME_KEY => 'a1',
            CollectorExtensionPriorityQueue::OBJ_KEY  => $this->getStubForContextCollectorExtensionInterface([])
        ], 1);

        $collectorExtensionQueue->insert([
            CollectorExtensionPriorityQueue::NAME_KEY => 'a1',
            CollectorExtensionPriorityQueue::OBJ_KEY  => $this->getStubForContextCollectorExtensionInterface([])
        ], 1);
    }

    public function testAdditionalContext()
    {
        $collectorExtensionQueue = new CollectorExtensionPriorityQueue();
        $collectorExtensionQueue->registerExtension('ext1', $this->getStubForContextCollectorExtensionInterface([
            'a' => 'a',
            'b' => 'b'
        ]));
        $this->assertEquals(['a' => 'a', 'b' => 'b'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));


        $collectorExtensionQueue->registerExtension('ext2', $this->getStubForContextCollectorExtensionInterface([
            'c' => 'c',
            'a' => 'd'
        ]));
        $this->assertEquals(['b' => 'b', 'c' => 'c', 'a' => 'd'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));

        /** Ignore because stub implements only CollectorExtensionInterface */
        $collectorExtensionQueue->registerExtension('ext3', $this->getStubForCollectorExtensionInterface());
        $this->assertEquals(['b' => 'b', 'c' => 'c', 'a' => 'd'], $collectorExtensionQueue->getAdditionalContext(new \Exception()));
    }

    /****************
     * STUBS
     ***************/
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


    /***********************
     * DATA PROVIDERS
     ***********************/
    public function invalidDataPreparedForInsert()
    {
        return [
            [ [CollectorExtensionPriorityQueue::NAME_KEY, CollectorExtensionPriorityQueue::OBJ_KEY] ],
            [ ['str'] ],
            [ [null] ],
            [ [CollectorExtensionPriorityQueue::NAME_KEY => ''] ],
            [ [CollectorExtensionPriorityQueue::OBJ_KEY => ''] ]
        ];
    }

    public function invalidObjectDataForInsert()
    {
        return [
            [ 'ext1', new \stdClass() ],
            [ 'ext1', 'test' ],
            [ 'ext1', 1 ],
            [ 'ext1', [] ],
        ];
    }
}