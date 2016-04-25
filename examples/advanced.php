<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTracker\BugHandler;
use DreamCommerce\BugTracker\Collector\Psr3Collector;
use DreamCommerce\BugTracker\Exception\ContextInterface;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

class TestException extends \Exception implements ContextInterface
{
    /**
     * @return array
     */
    public function getContext()
    {
        return array(
            'field_a' => 'A',
            'field_b' => 'B'
        );
    }
}

class Test1Exception extends TestException {}
class Test2Exception extends TestException {}

class FirstCollector extends Psr3Collector
{
    /**
     * {@inheritdoc}
     */
    public function hasSupportException($exc, $level, array $context = array())
    {
        return  parent::hasSupportException($exc, $level, $context) &&
                $exc instanceof Test1Exception;
    }
}

class SecondCollector extends Psr3Collector
{
    /**
     * {@inheritdoc}
     */
    public function hasSupportException($exc, $level, array $context = array())
    {
        return  parent::hasSupportException($exc, $level, $context) &&
                isset($context['field_a']) && $context['field_a'] == 'A';
    }
}

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/first_advanced.log'));

BugHandler::registerCollector(new FirstCollector(array(
    'logger' => $logger
)));

$logger2 = new Logger('test');
$logger2->pushHandler(new StreamHandler(__DIR__ . '/logs/second_advanced.log'));

BugHandler::registerCollector(new SecondCollector(array(
    'logger' => $logger2
)));

BugHandler::enable(E_ALL, false);

try {
    throw new TestException('test');
} catch(\Exception $exc) {
    BugHandler::handle($exc);
}