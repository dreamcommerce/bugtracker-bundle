# DreamCommerce BugTracker Bundle

[![License](https://img.shields.io/packagist/l/dreamcommerce/bugtracker-bundle.svg)](https://packagist.org/packages/dreamcommerce/bugtracker-bundle)
[![Version](https://img.shields.io/packagist/vpre/dreamcommerce/bugtracker-bundle.svg)](https://packagist.org/packages/dreamcommerce/bugtracker-bundle)
[![Build status on Linux](https://img.shields.io/travis/dreamcommerce/bugtracker-bundle/master.svg)](http://travis-ci.org/dreamcommerce/bugtracker-bundle)

## Changelog

``1.4.0``
  - added support for extending context for bugtracker collectors

``1.3.4``
  - bump minimum Symfony version to 2.8
  - many fixes

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


## Installation (Standalone)

### Installing the lib/bundle

Simply run assuming you have installed composer.phar or composer binary:

``` bash
$ composer require dreamcommerce/bugtracker-bundle
```

### Additional libraries

If you want to enable other handlers than PSR-3 handler, you must also install additional packages:

#### JIRA:

``` bash
$ composer require guzzlehttp/guzzle
$ composer require zendframework/zend-json
```

#### Doctrine

``` bash
$ composer require doctrine/orm
```

#### Swiftmailer

``` bash
$ composer require swiftmailer/swiftmailer
```

## Installation (In Symfony 3 Application)

### Enable the bundle

Enable the bundle in the kernel:

``` php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        //...
            new DreamCommerce\Bundle\CommonBundle\DreamCommerceCommonBundle(),
            new DreamCommerce\Bundle\BugTrackerBundle\DreamCommerceBugTrackerBundle(),
        //...
    );
    return $bundles;
}
```

## Minimal configuration

```yaml
dream_commerce_bug_tracker:
    configuration:
        jira:
            entry_point: "https://jira.example.com"
            username: "login"
            password: "*****"
            project: "PROJECT_SYMBOL"
        swiftmailer:
            sender: "%mailer_user%"
            recipients:
                - bugtracker@example.com

    collectors:
        psr3:
            type: psr3

        jira:
            type: jira
            
        doctrine:
            type: doctrine
            
        swiftmailer:
            type: swiftmailer
```

## Advanced configuration:

```yaml
dream_commerce_bug_tracker:
    configuration:
        base:
            token_generator: dream_commerce_bug_tracker.token_generator
            use_token: false
            ignore_exceptions:
                - 'RuntimeException'
            exceptions:
                - 'FooException'
                - 'BarException'
        jira:
            entry_point: "https://jira.example.com"
            username: "login"
            password: "*****"
            project: "PROJECT_SYMBOL"
            labels: [ "app_test" ]
            assignee: "my.login"
            token_generator: dream_commerce_bug_tracker.token_generator
            use_token: true
            ignore_exceptions:
                - 'RuntimeException1'
            exceptions:
                - 'FooException1'
                - 'BarException1'
          
        doctrine:
            model: AppBundle\Entity\UserError
            token_generator: dream_commerce_bug_tracker.token_generator
            use_token: true
            
        swiftmailer:
            sender: "%mailer_user%"
            recipients:
                - bugtracker@example.com
            ignore_exceptions:
                - 'RuntimeException2'
            exceptions:
                - 'FooException2'
                - 'BarException2'
            token_generator: dream_commerce_bug_tracker.token_generator
            use_token: false
                        
    collectors:
        psr3:
            type: psr3
            class: DreamCommerce\Component\BugTracker\Collector\Psr3Collector
            priority: 100
            options:
                ignore_exceptions:
                    - 'ErrorException'
                exceptions:
                    - 'MyException'
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

## Usage

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

## Examples 

### Doctrine:

```php
    <?php
    
    namespace AppBundle\Entity;
    
    use DreamCommerce\Component\BugTracker\Model\Error;
    
    class UserError extends Error
    {
    
    }

```

```xml
    <?xml version="1.0" encoding="utf-8"?>
    <doctrine-mapping xmlns="http://doctrine-project.org/schemas/orm/doctrine-mapping" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://doctrine-project.org/schemas/orm/doctrine-mapping http://doctrine-project.org/schemas/orm/doctrine-mapping.xsd">
      <entity repository-class="DreamCommerce\Component\BugTracker\Repository\ORMErrorRepository" name="AppBundle\Entity\UserError" table="user_errors">
            <!-- ... -->
      </entity>
    </doctrine-mapping>
```

### Custom

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


## Extending
Bug Tracker can inject additional context to exceptions from other packages without modifying them. These context is visible in logs and it can help diagnose errors.
It can be realise by simple service implements suitable interface with special tag.
For more information look for below examples.

First you need to create service that implements **ContextCollectorExtensionInterface** interface
Class that implement ContextCollectorExtensionInterface need one method called **getAdditionalContext**
For details look to the interface file
```php
    <?php
    namespace AppBundle\BugTracker\Extension;
    
    use DreamCommerce\Component\BugTracker\Collector\Extension\ContextCollectorExtensionInterface;
    use Symfony\Component\HttpKernel\Exception\HttpException;
    use Symfony\Component\HttpFoundation\RequestStack;
    
    class ContextCollectorExtension implements ContextCollectorExtensionInterface
    {
        /**
         * @var RequestStack 
         */
        private $requestStack;
        
        public function __construct(RequestStack $requestStack) {
            $this->requestStack = $requestStack;
        }
        
        public function getAdditionalContext(\Throwable $throwable): array
        {
            if ($throwable instanceof HttpException) {
                $request = $this->requestStack->getMasterRequest();
                
                if ($request === null) {
                    return [];
                }
                
                return [
                    'query'     => serialize($request->query->all()),
                    'client_ip' => $request->server->get('REMOTE_ADDR')
                ];
            }
            
            return [];
        }
    }
```

Next step is create our service definition with tag **dream_commerce_bug_tracker.collector_extension**
```xml
    ...
    <service id="app.bug_tracker_extension" class="AppBundle\BugTracker\Extension\ContextCollectorExtension">
        <argument type="service" id="request_stack" />

        <tag name="dream_commerce_bug_tracker.collector_extension" />
    </service>
    ...
```

Now whenever HttpException will be threw we will see our additional information in log files.

If you want you can use our embeded extensions:
* **ClientIpContextExtension** - append to log client ip
```xml
<service id="app.bug_tracker_client_ip_extension" class="DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension\ClientIpContextExtension">
    <argument type="service" id="request_stack" />
    <tag name="dream_commerce_bug_tracker.collector_extension" />
</service>
```

* **QueryDataContextExtension** - append to log parameters stored in $_GET variable
```xml
<service id="app.bug_tracker_client_ip_extension" class="DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension\QueryDataContextExtension">
    <argument type="service" id="request_stack" />
    <tag name="dream_commerce_bug_tracker.collector_extension" />
</service>
```

* **RequestDataContextExtension** - append to log parameters stored in $_POST variable
```xml
<service id="app.bug_tracker_client_ip_extension" class="DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension\RequestDataContextExtension">
    <argument type="service" id="request_stack" />
    <tag name="dream_commerce_bug_tracker.collector_extension" />
</service>
```

* **UserInfoContextExtension** - append to log information from security component about current user(login, role, credentials etc.)
```xml
<service id="app.bug_tracker_client_ip_extension" class="DreamCommerce\Bundle\BugTrackerBundle\Collector\Extension\UserInfoContextExtension">
    <argument type="service" id="security.token_storage" />
    <tag name="dream_commerce_bug_tracker.collector_extension" />
</service>
```

## Authors

* Michał Korus <michal.korus@dreamcommerce.com>
* Daniel Hornik <daniel.1302@gmail.com>