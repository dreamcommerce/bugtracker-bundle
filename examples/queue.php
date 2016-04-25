<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTracker\Collector\Psr3Collector;
use DreamCommerce\BugTracker\Collector\QueueCollector;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$loggerFirst = new Logger('test');
$loggerFirst->pushHandler(new StreamHandler(__DIR__ . '/logs/queue_first.log', Logger::WARNING));

$queue = new QueueCollector();
$queue->registerCollector(new Psr3Collector(array(
    'logger' => $loggerFirst
)));

$loggerSecond = new Logger('test');
$loggerSecond->pushHandler(new StreamHandler(__DIR__ . '/logs/queue_second.log', Logger::WARNING));

$queue->registerCollector(new Psr3Collector(array(
    'logger' => $loggerSecond
)));

try {
    throw new \Exception('test');
} catch(\Exception $exc) {
    $queue->handle($exc);
}