# DreamCommerce BugTracker Bundle

Changelog
---------

``1.1.1``
   - improved marking collectors as collected
   - fixed custom collector
   - added more examples

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
             
          custom_1:
              type: custom
              class: AppBundle\BugTracker\CustomCollector
              options:
                  foo: 1
                  bar: 2           
```

Example Doctrine model:
------------

```php
    <?php
    
    namespace AppBundle\Entity;
    
    use DreamCommerce\Component\BugTracker\Model\Error;
    
    class UserError extends Error
    {
    
    }

```

Example Doctrine mapping:
------------

```xml
    <?xml version="1.0" encoding="utf-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
      <entity repository-class="DreamCommerce\Bundle\BugTrackerBundle\Doctrine\ORM\Repository\ErrorRepository" name="AppBundle\Entity\UserError" table="user_errors">
            <!-- ... -->
      </entity>
    </doctrine-mapping>
```

Example custom collector
------------
```php
    <?php
    
    namespace AppBundle\BugTracker;
    
    use DreamCommerce\Component\BugTracker\Collector\CollectorInterface;
    use DreamCommerce\Component\BugTracker\Traits\Options;
    use Psr\Log\LogLevel;
    
    class CustomCollector implements CollectorInterface
    {
        use Options;
    
        private $collected = false;
    
        private $foo;
    
        private $bar;
    
        public function __construct(array $options = array())
        {
            $this->setOptions($options);
        }
    
        public function hasSupportException($exception, $level = LogLevel::WARNING, array $context = array())
        {
            return is_object($exception) && $exception instanceof \RuntimeException;
        }
    
        public function handle($exception, $level = LogLevel::WARNING, array $context = array())
        {
            echo $exception->getMessage() . '; foo: ' . $this->foo . '; bar: ' . $this->bar;
            $this->collected = true;
        }
    
        public function isCollected()
        {
            return $this->collected;
        }
    
        public function setFoo($foo)
        {
            $this->foo = $foo;
        }
    
        public function setBar($bar)
        {
            $this->bar = $bar;
        }
    }
```

Example code:
------------
```php
        <?php
        
        use Psr\Log\LogLevel;
        
        // use all collectors
        
        try {
            throw new \RuntimeException();
        } catch(\Exception $exc) {
            $this->get('bug_tracker')->handle($exc, LogLevel::ERROR, array('a' => 1, 'b' => 2, 'c' => 3, 'd' => new \stdClass()));
        }
        
        // use only one collector
        
        try {
            throw new \RuntimeException();
        } catch(\Exception $exc) {
            $this->get('dream_commerce_bug_tracker.collector.custom_1')->handle($exc);
        }
```