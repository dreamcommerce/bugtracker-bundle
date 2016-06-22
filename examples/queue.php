<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTrackerBundle\Collector\Psr3Collector;
use DreamCommerce\BugTrackerBundle\Collector\QueueCollector;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$loggerFirst = new Logger('test');
$loggerFirst->pushHandler(new StreamHandler(__DIR__ . '/logs/queue_first.log', Logger::WARNING));

$queue = new QueueCollector();
$queue->registerCollector(new Psr3Collector($loggerFirst));

$loggerSecond = new Logger('test 2');
$loggerSecond->pushHandler(new StreamHandler(__DIR__ . '/logs/queue_second.log', Logger::WARNING));

$queue->registerCollector(new Psr3Collector($loggerSecond));

try {
    throw new \Exception('test');
} catch(\Exception $exc) {
    $queue->handle($exc);
}