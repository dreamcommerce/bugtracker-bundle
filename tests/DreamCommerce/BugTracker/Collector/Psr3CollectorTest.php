<?php

namespace DreamCommerceTest\BugTracker\Collector;

use DreamCommerce\BugTracker\Collector\Psr3Collector;

class Psr3CollectorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Psr3Collector
     */
    private $_collector;

    public function setUp()
    {
        $this->_collector = new Psr3Collector();
    }

    public function testPreFormattedLogger()
    {
        // TODO
    }
}