<?php

namespace Mintdev\Xml\Logger;

use Carbon\Carbon;
use Mintdev\Xml\Logger\Types\Step;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class SoapLogger
{

    private Logger $logger;

    public function __construct(private StreamHandler|RotatingFileHandler $handler,
        FormatterInterface $formatter,
        private readonly string $serviceName,
        private readonly ?string $methodName,
        private readonly ?string $correlationId)
    {
        $this->logger = new Logger("splunk");
        $this->handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);
    }

    private function date(): string
    {
        return Carbon::now()->format('d-j-Y G:i:s:v');
    }

    public function log(string $message, Step $step): void
    {
        $soapAction = $_SERVER['HTTP_SOAPACTION'] ?? "n/a";

        $this->logger->info($step->value, [
            'date' => $this->date(),
            'serviceName' => $this->serviceName,
            'methodName' => $this->methodName ?? "n/a",
            'correlationId' => $this->correlationId ?? "n/a",
            'soapAction' => $soapAction,
            'step' => $step->value,
            'message' => $message,
        ]);
    }

}