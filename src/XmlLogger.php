<?php

namespace Mintdev\Xml\Logger;

use Carbon\Carbon;
use Monolog\Formatter\FormatterInterface;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class XmlLogger
{

    private Logger $logger;

    public function __construct(string $logFile,
        private StreamHandler|RotatingFileHandler $handler,
        FormatterInterface $formatter,
        private readonly string $serviceName,
        private readonly ?string $methodName)
    {
        $this->logger = new Logger("splunk");
        $this->handler->setFormatter($formatter);
        $this->logger->pushHandler($handler);
    }

    private function data(): string
    {
        return Carbon::now()->format('d-j-Y G:i:s:v');
    }

    public function logInputXml(): void
    {
        $request = file_get_contents('php://input');
        $soapAction = $_SERVER['HTTP_SOAPACTION'] ?? "n/a";

        $this->logger->info("Request", [
            'data' => $this->data(),
            'servizio' => $this->serviceName,
            'metodo' => $this->methodName ?? "n/a",
            'soapAction' => $soapAction,
            'step' => 'FROM_CALLER_TO_SERVICE',
            'messaggio' => $request,
        ]);
    }

    public function logOutputXml(string $response): void
    {
        $this->logger->info("Response", [
            'data' => $this->data(),
            'servizio' => $this->serviceName,
            'metodo' => $this->methodName ?? "n/a",
            'step' => 'FROM_SERVICE_TO_CALLER',
            'messaggio' => $response,
        ]);
    }

    public function logGenerico(string $message, $step): void
    {
        $this->logger->info($step, [
            'data' => $this->data(),
            'servizio' => $this->serviceName,
            'metodo' => $this->methodName ?? "n/a",
            'step' => $step,
            'messaggio' => $message,
        ]);
    }

}