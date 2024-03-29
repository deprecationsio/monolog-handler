# deprecationsio/monolog-handler

deprecationsio/monolog-handler is a Monolog handler integrating 
[deprecations.io](https://github.com/deprecationsio/deprecations.io)
with Monolog applications.

deprecations.io is a plug-and-play service to monitor and update your usages of deprecated features from your 
vendors, keeping your code ready for every major version to come. 

Keeping your code up-to-date was never this easy!

## Installation

deprecationsio/monolog-handler requires PHP 5.3+. 

```
composer require deprecationsio/monolog-handler
```

## Usage

This library provides a Monolog handler for Monolog 1, 2 and 3. 

To use it manually, use the following code:

```php
use Deprecationsio\Monolog\Client\CurlDeprecationsioClient;
use Deprecationsio\Monolog\Handler\MonologV1Handler;
use Deprecationsio\Monolog\Handler\MonologV2Handler;
use Deprecationsio\Monolog\Handler\MonologV3Handler;
use Monolog\Logger;

$logger = new Logger('app', [
    // Monolog 1
    new MonologV1Handler('<your-deprecations.io-endpoint>'),
    
    // Monolog 2
    new MonologV2Handler('<your-deprecations.io-endpoint>'),
    
    // Monolog 3
    new MonologV3Handler('<your-deprecations.io-endpoint>'),
]);

// Log a deprecation
// A deprecation must contain an exception that will be used by developers to understand the stacktrace
$logger->notice('User Deprecated: ...', [
    'exception' => $this->createDeprecationException(),
]);
```

You can also use the provided class name resolver to load the handler dynamically depending
on the Monolog version you are using:

```php
use Deprecationsio\Monolog\MonologHandlerClassNameResolver;

$handlerName = MonologHandlerClassNameResolver::resolveHandlerClassName();

$logger = new Logger('app', [
    new $handlerName('<your-deprecations.io-endpoint>'),
]);
```
