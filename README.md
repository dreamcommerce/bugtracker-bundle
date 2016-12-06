# DreamCommerce BugTracker Bundle

 Example config.yml:
------------
```yaml
 dream_commerce_bug_tracker:
     configuration:
         jira:
             entry_point: "https://jira.example.com"
             username: "login"
             password: "*****"
             project: "PROJECT_SYMBOL"
             labels: [ "app_test" ]
             assignee: "my.login"
     collectors:
         psr3:
             type: psr3
             class: DreamCommerce\Component\BugTracker\Collector\Psr3Collector
             priority: 100
             options:
                 ignore_exceptions:
                     - \ErrorException
         jira:
             type: jira
             class: DreamCommerce\Component\BugTracker\Collector\JiraCollector
             level: error
             priority: -100
```

Example code:
------------
```php
        try {
            throw new \RuntimeException();
        } catch(\Exception $exc) {
            $this->get('bug_tracker')->handle($exc, LogLevel::ERROR, array('a' => 1, 'b' => 2, 'c' => 3, 'd' => new \stdClass()));
        }
```