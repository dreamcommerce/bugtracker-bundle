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
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

$logger = new Logger('test');
$logger->pushHandler(new StreamHandler(__DIR__ . '/logs/simple.log', Logger::WARNING));

$collector = new Psr3Collector(array('logger' => $logger));

try {
    throw new \Exception('test');
} catch (\Exception $exc) {
    $collector->handle($exc);
}
