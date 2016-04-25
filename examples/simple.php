<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTracker\BugHandler;
use DreamCommerce\BugTracker\Collector\Psr3Collector;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/simple.log', Logger::WARNING));

BugHandler::registerCollector(new Psr3Collector(array(
    'logger' => $logger
)));
BugHandler::enable(E_ALL, false);

try {
    throw new \Exception('test');
} catch(\Exception $exc) {
    BugHandler::handle($exc);
}