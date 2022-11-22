<?php

use Mintdev\Xml\Logger\RotateOnFileSizeHandler;
use Monolog\Level;

use function Pest\Faker\faker;
use function PHPUnit\Framework\assertFileDoesNotExist;
use function PHPUnit\Framework\assertFileExists;

$baseDir = "./tests/temp";

beforeEach(function () use ($baseDir){
    array_map('unlink', glob("$baseDir/*.log"));
});

afterAll(function () use ($baseDir){
    array_map('unlink', glob("$baseDir/*.log"));
});

test('log creation works', function () use ($baseDir){
    $logTemp = $baseDir.'/test.log';

    $handler = RotateOnFileSizeHandler::make($logTemp,10,1, Level::Info);

    $logger = new Monolog\Logger($logTemp);
    $logger->pushHandler($handler);

    $logger->log(Level::Info,faker()->text(1000));

    assertFileExists($logTemp);
});

test('log rotation works', function () use ($baseDir){
    $logTemp = $baseDir.'/test.log';
    $expectedLogRotation = $baseDir.'/test-1.log';

    // first generation
    $handler = RotateOnFileSizeHandler::make($logTemp,10,2, Level::Info);

    $logger = new Monolog\Logger($logTemp);
    $logger->pushHandler($handler);

    $logger->log(Level::Info,faker()->text(1000));

    // second generation
    $handler = RotateOnFileSizeHandler::make($logTemp,10,2, Level::Info);
    $logger = new Monolog\Logger($logTemp);
    $logger->pushHandler($handler);

    $logger->log(Level::Info,faker()->text(1000));

    assertFileExists($expectedLogRotation);
});

test('log rotation deletes older files', function () use ($baseDir){
    $logTemp = $baseDir.'/test.log';
    $expectedLogRotation = $baseDir.'/test-2.log';

    // first generation
    $handler = RotateOnFileSizeHandler::make($logTemp,10,1, Level::Info);

    $logger = new Monolog\Logger($logTemp);
    $logger->pushHandler($handler);

    $logger->log(Level::Info,faker()->text(1000));

    // second generation
    $handler = RotateOnFileSizeHandler::make($logTemp,10,1, Level::Info);

    $logger = new Monolog\Logger($logTemp);
    $logger->pushHandler($handler);

    $logger->log(Level::Info,faker()->text(1000));

    // third generation
    $handler = RotateOnFileSizeHandler::make($logTemp,10,1, Level::Info);

    $logger = new Monolog\Logger($logTemp);
    $logger->pushHandler($handler);

    $logger->log(Level::Info,faker()->text(1000));

    assertFileDoesNotExist($expectedLogRotation);
});