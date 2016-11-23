# DreamCommerce BugTracker Bundle

 Example config.yml:
------------
```yaml
 monolog:
     handlers:
         file_log:
             type: stream
             path: "%kernel.logs_dir%/%kernel.environment%.log"
             level: notice
 
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
