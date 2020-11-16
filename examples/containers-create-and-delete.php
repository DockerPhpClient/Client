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

$container->setAttachStdout(true);

$containerName = 'test-container';
try {
//    // create a named container
//    $client->containers()->create($container, ['name' => $containerName]);
//
//
//    // list all images
//    $containers = $client->containers()->list();
//    echo implode("", $client->containers()->toContainerListLog($containers));
//
//    // start container
//    $client->containers()->start($containerName);
//
//    // wait for container to complete (exit) (blocking)
//    $client->containers()->wait($containerName);

    // get all logs from the stopped container
    // echo $client->containers()->logs($containerName);
    echo $client->containers()->logs("repeat");

    // var_dump($client->containers()->logsResponse("repeat")->getHeader('Content-Type'));
    $stream = $client->containers()->logsResponse("repeat");
    $body = $stream->getBody();
    //$body->rewind();
    var_dump($body->isSeekable());
    // var_dump(read($body, 8));
} catch(Exception $e) {
    echo $e->getMessage();
} finally {
    // delete container
    die();
    $client->containers()->delete($containerName);
}

function read($stream, $length) {
    $read = '';

    do {
        $read .= $stream->read($length - \strlen($read));
    } while (\strlen($read) < $length && !$stream->eof());

    return $read;
}
