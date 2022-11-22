# PHP Logger for Soap Web Services

## Installation

Installation is possible using Composer

```
composer require marco-introini/soap-logger
```

## Usage

### Log

The main logger class can be instantiated using the following parameters:

- a monolog handler
- a line formatter
- the name of the Soap service
- the name of the method
- if the request contains soapAction this information will be automatically added to logger

```php
$handler = new RotatingFileHandler($_ENV['LOGFILE'], 3,Level::Info);
$formatter = new SplunkLineFormatter(allowInlineLineBreaks: true, quoteReplacement: "");

$soapLogger = new SoapLogger($handler,$formatter,"MyDemoService","demoMethodRead");

$soapLogger->log(file_get_contents('php://input'),Step::FROM_CALLER_TO_SERVICE);
```

### Log SoapServer Request and Response XML

This is how to het the Soap envelope for request and response

#### Request

Obtaining request it's very easy:

```php
$request = file_get_contents('php://input');
$soapLogger->log(file_get_contents('php://input'),Step::FROM_CALLER_TO_SERVICE);
```

#### Response

Obtaining response it's a bit more difficult. We must catch the output of the handle() method. Look at this example:

```php
ob_start();
$server = new SoapServer("mywsdlfile.wsdl", array(
    'classmap' => array(
        'MyMethodResponse' => MyMethodResponse::class,
        'MyMethodRequest' => MyMethodRequest::class
    )
));
$server->addFunction("myMethod");
$server->handle();
$response = ob_get_contents();
ob_end_clean();
$soapLogger->log($response,Step::FROM_SERVICE_TO_CALLER);
echo $response;
```

In the $response variable we have the response XML.

**Important: always remeber to echo the response!**

### Rotation based on file size

Very often the Soap log are very verbose, so it's useful to have a rotation method for Monolog to log rotation based on file size.
This is not possible with plain Monolog. So I write a simple factory to generate a Monolog Handler which can do a log rotation.

The make factory accept these parameters:

- logfile path
- maximum size in bytes
- number of files to rotate (will be kept these number of log-rotation)
- log Level

```php
$handler = RotateOnFileSizeHandler::make($_ENV['LOGFILE'],50000000,1,Level::Info);
$formatter = new SplunkLineFormatter(allowInlineLineBreaks: true, quoteReplacement: "");

$soapLogger = new SoapLogger($handler,$formatter,"MyDemoService","myMethod");

$soapLogger->log(file_get_contents('php://input'),Step::FROM_CALLER_TO_SERVICE);
```

Using standard Monolog Logger:

```php
$handler = RotateOnFileSizeHandler::make($logfile, 1000, 1, Level::Info);

$logger = new Monolog\Logger($logTemp);
$logger->pushHandler($handler);

$logger->log(Level::Info,'log message');
```

## Test

Tests are created using Pest

```
./vendor/bin/pest
```

## License

This project is licensed as Open Source under MIT license