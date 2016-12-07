# DreamCommerce BugTracker Bundle

Changelog
---------

``1.1.0``
   - added doctrine collector
   - added swiftmailer collector
   - improved jira collector


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
             
         swiftmailer:
             sender: "%mailer_user%"
             recipients:
                 - bugtracker@example.com
                         
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
             
         doctrine:
             type: doctrine
             class: DreamCommerce\Component\BugTracker\Collector\DoctrineCollector
             options:
                 model: AppBundle\Entity\UserError
 
         swiftmailer:
             type: swiftmailer
             class: DreamCommerce\Component\BugTracker\Collector\SwiftMailerCollector
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