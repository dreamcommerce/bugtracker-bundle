<?php

/*
 * (c) 2017 DreamCommerce
 *
 * @package DreamCommerce\Component\BugTracker
 * @author Michał Korus <michal.korus@dreamcommerce.com>
 * @link https://www.dreamcommerce.com
 */

require_once __DIR__ . '/../vendor/autoload.php';

use DreamCommerce\Component\BugTracker\Collector\JiraCollector;
use DreamCommerce\Component\BugTracker\Connector\JiraConnector;
use DreamCommerce\Component\Common\Http\GuzzleClient;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;

$client = new Client();
$httpClient = new GuzzleClient($client);

$options = array(
    'connector' => new JiraConnector($httpClient),
    'entry_point' => 'https://jira.example.com',
    'username' => '...',
    'password' => '...',
    'project' => '...',
    'counter_field_id' => 88888,
    'token_field_id' => 99999,
    'default_priority' => 2, // normal
    'assignee' => '...',
    'priorities' => array(
        LogLevel::WARNING => 1, // minor
        LogLevel::ERROR => 2, // normal
        LogLevel::ALERT => 3, // major
        LogLevel::CRITICAL => 4, // critical
        LogLevel::EMERGENCY => 5 // blocker
    ),
    'default_type' => 1, // bug
    'types' => array(
        LogLevel::EMERGENCY => 3 // emergency
    )
);

$collector = new JiraCollector($options);

try {
    throw new \Exception('test');
} catch (\Exception $exc) {
    $collector->handle($exc, LogLevel::WARNING, array('jira' => 'test'));
}
