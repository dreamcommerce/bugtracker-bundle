# DreamCommerce BugTracker Bundle

Changelog
---------

``1.3.0``
  - added more tests
  - change collector service name to dream_commerce_bug_tracker.< name >_collector

``1.2.2``
    
  - fixed Doctrine collector
  - fixed bundle configuration

``1.2.1``

  - move ContextInterface to dreamcommerce/common-bundle

``1.2.0``

   - library is no longer supported on PHP 5
   - added doctrine DBAL types

``1.1.2``
   - added context trait

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

Example Doctrine ORM mapping:
------------

```xml
    <?xml version="1.0" encoding="utf-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
      <entity repository-class="DreamCommerce\Component\BugTracker\Repository\ORMErrorRepository" name="AppBundle\Entity\UserError" table="user_errors">
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
    use DreamCommerce\Component\Common\Model\ArrayableInterface;
    use DreamCommerce\Component\Common\Model\ArrayableTrait;
    use Psr\Log\LogLevel;
    use Throwable;
    
    class CustomCollector implements CollectorInterface, ArrayableInterface
    {
        use ArrayableTrait;
    
        private $collected = false;
    
        private $foo;
    
        private $bar;
    
        public function __construct(array $options = array())
        {
            $this->fromArray($options);
        }
    
        public function hasSupportException(Throwable $exception, string $level = LogLevel::WARNING, array $context = array()): bool
        {
            return is_object($exception) && $exception instanceof \RuntimeException;
        }
    
        public function handle(Throwable $exception, string $level = LogLevel::WARNING, array $context = array())
        {
            echo $exception->getMessage() . '; foo: ' . $this->foo . '; bar: ' . $this->bar;
            $this->collected = true;
        }
    
        public function isCollected(): bool
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
            throw new RuntimeException();
        } catch(Exception $exc) {
            $this->get('bug_tracker')->handle($exc, LogLevel::ERROR, array('a' => 1, 'b' => 2, 'c' => 3, 'd' => new stdClass()));
        }
        
        // use only one collector
        
        try {
            throw new RuntimeException();
        } catch(Exception $exc) {
            $this->get('dream_commerce_bug_tracker.collector.custom_1')->handle($exc);
        }
```