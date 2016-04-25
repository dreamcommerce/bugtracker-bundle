<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTracker\BugHandler;
use DreamCommerce\BugTracker\Collector\Psr3Collector;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$firstLogger = new Logger('test');
$firstLogger->pushHandler(new StreamHandler(__DIR__ . '/logs/first_multiple_collectors.log'));

BugHandler::registerCollector(new Psr3Collector(array(
    'logger' => $firstLogger,
    'ignore_exceptions' => array(
        \RuntimeException::class
    )
)));

$secondLogger = new Logger('test');
$secondLogger->pushHandler(new StreamHandler(__DIR__ . '/logs/second_multiple_collectors.log'));

BugHandler::registerCollector(new Psr3Collector(array(
    'logger' => $secondLogger,
    'exceptions' => array(
        \RuntimeException::class
    )
)));

BugHandler::enable(E_ALL, false);

try {
    throw new \RuntimeException('test');
} catch(\Exception $exc) {
    BugHandler::handle($exc);
}