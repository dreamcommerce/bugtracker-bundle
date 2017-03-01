<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DreamCommerce\Component\BugTracker\Collector\Psr3Collector;
use DreamCommerce\Component\BugTracker\Collector\QueueCollector;
use DreamCommerce\Component\Common\Exception\ContextInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LogLevel;

class TestException extends Exception implements ContextInterface
{
    /**
     * @return array
     */
    public function getExceptionContext(): array
    {
        return array(
            'field_a' => 'A',
            'field_b' => 'B'
        );
    }
}

class Test1Exception extends TestException
{
}
class Test2Exception extends TestException
{
}

class FirstCollector extends Psr3Collector
{
    /**
     * {@inheritdoc}
     */
    protected function _hasSupportException(Throwable $exception, string $level = LogLevel::WARNING, array $context = array()): bool
    {
        return $exception instanceof Test1Exception;
    }
}

class SecondCollector extends Psr3Collector
{
    /**
     * {@inheritdoc}
     */
    protected function _hasSupportException(Throwable $exception, string $level = LogLevel::WARNING, array $context = array()): bool
    {
        return isset($context['field_a']) && $context['field_a'] == 'A';
    }
}

$queue = new QueueCollector();

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/first_advanced.log'));

$queue->registerCollector(new FirstCollector(array('logger' => $logger)));

$logger2 = new Logger('test');
$logger2->pushHandler(new StreamHandler(__DIR__ . '/logs/second_advanced.log'));

$queue->registerCollector(new SecondCollector(array('logger' => $logger2)));

try {
    throw new TestException('test');
} catch (\Exception $exc) {
    $queue->handle($exc);
}
