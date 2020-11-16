<?php

use Docker\Client\Builder\ContainerBuilder;
use Docker\Client\DockerClientFactory;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

require_once __DIR__ . '/../vendor/autoload.php';

class StdoutLogger implements LoggerInterface
{

    public function emergency($message, array $context = array())
    {
        $this->log("emergency", $message, $context);
    }

    public function alert($message, array $context = array())
    {
        $this->log("alert", $message, $context);
    }

    public function critical($message, array $context = array())
    {
        $this->log("critical", $message, $context);
    }

    public function error($message, array $context = array())
    {
        $this->log("error", $message, $context);
    }

    public function warning($message, array $context = array())
    {
        $this->log("warning", $message, $context);
    }

    public function notice($message, array $context = array())
    {
        $this->log("notice", $message, $context);
    }

    public function info($message, array $context = array())
    {
        $this->log("info", $message, $context);
    }

    public function debug($message, array $context = array())
    {
        $this->log("debug", $message, $context);
    }

    public function log($level, $message, array $context = array())
    {
        echo "[${level}] ${message}";
    }
}



$client = DockerClientFactory::create();

// create container value object with volume definition
$container = (new ContainerBuilder())
    ->fromImage('php:7.4-cli')
    ->addBind(__DIR__ . '/../', '/data', 'rw')
    ->entryPoint(['ls', '-al', '/data'])
    ->build();

$containerName = 'test-container';
try {
    // create a named container
    $client->containers()->create($container, ['name' => $containerName]);


    // list all images
    $containers = $client->containers()->list();
    foreach ($containers as $container) {
        echo $container->getId() . "\n";
    }

    // start container
    $client->containers()->start($containerName);

    // wait for container to complete (exit) (blocking)
    $client->containers()->wait($containerName);

    // get all logs from the stopped container
    $logger = new StdoutLogger();
    $client->containers()->log($containerName, $logger);
    $client->containers()->log($containerName, $logger, LogLevel::DEBUG);
} catch(Exception $e) {
    echo $e->getMessage();
} finally {
    // delete container
    $client->containers()->delete($containerName);
}
