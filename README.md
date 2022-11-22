# Soap logger for Web Services

## Installation

Installation is possible using Composer

```
composer require marco-introini/soap-logger
```

## Usage

### Log

```php
$handler = new RotatingFileHandler($_ENV['LOGFILE'], 3,Level::Info);
$formatter = new SplunkLineFormatter(allowInlineLineBreaks: true, quoteReplacement: "");

$soapLogger = new SoapLogger($handler,$formatter,"InvitationService","statoRichiestaRead");

$soapLogger->log(file_get_contents('php://input'),Step::FROM_CALLER_TO_SERVICE);
```

### Rotation based on file size

See this example

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