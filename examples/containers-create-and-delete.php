<?php

use Docker\Client\Builder\ContainerBuilder;
use Docker\Client\DockerClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

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

    // start container
    $client->containers()->start($containerName);

    // wait for container to complete (exit) (blocking)
    $client->containers()->wait($containerName);

    // get all logs from the stopped container
    echo $client->containers()->logs($containerName);
} catch(Exception $e) {
    echo $e->getMessage();
} finally {
    // delete container
    $client->containers()->delete($containerName);
}
