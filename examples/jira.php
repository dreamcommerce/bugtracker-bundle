<?php

require_once '../vendor/autoload.php';

use DreamCommerce\BugTrackerBundle\Collector\JiraCollector;
use DreamCommerce\BugTrackerBundle\Http\Client\GuzzleClient;
use DreamCommerce\BugTrackerBundle\Jira\Client as JiraClient;
use DreamCommerce\BugTrackerBundle\Jira\Configuration as JiraConfiguration;
use GuzzleHttp\Client;
use Psr\Log\LogLevel;

$jiraClient = new JiraClient(new GuzzleClient(new Client()));
$jiraConfiguration = new JiraConfiguration(
    array(
        'entry_point' => 'https://jira.example.com',
        'username' => '...',
        'password' => '...',
        'project' => '...',
        'counter_field_id' => '...',
        'hash_field_id' => '...',
        'default_priority' => '...', // normal
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
    )
);

$collector = new JiraCollector($jiraClient, $jiraConfiguration);

try {
    throw new \Exception('test');
} catch(\Exception $exc) {
    $collector->handle($exc, LogLevel::EMERGENCY);
}