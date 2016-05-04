<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTracker\Collector\JiraCollector;
use DreamCommerce\BugTracker\Http\Client\GuzzleWrapper;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;

$httpClient = new GuzzleWrapper(new Client());
$collector = new JiraCollector(array(
    'http_client' => $httpClient,
    'entry_point' => 'https://jira.example.com',
    'username' => '...',
    'password' => '...',
    'project' => '...',
    'counter_field_id' => '...',
    'hash_field_id' => '...',
    'default_priority' => '...', // normal
    'in_progress_statuses' => array(
        //11, // To Do
        21, // In Progress
        31, // In Review
        //41, // Done
    ),
    'reopen_status' => 11,
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
));

try {
    throw new \Exception('test');
} catch(\Exception $exc) {
    $collector->handle($exc, LogLevel::EMERGENCY);
}