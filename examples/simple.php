<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTrackerBundle\Collector\Psr3Collector;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/simple.log', Logger::WARNING));

$collector = new Psr3Collector($logger);

try {
    throw new \Exception('test');
} catch(\Exception $exc) {
    $collector->handle($exc);
}