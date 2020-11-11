<?php

use Docker\Client\DockerClientFactory;

require_once __DIR__ . '/../vendor/autoload.php';

$client = DockerClientFactory::create();

// build image
$client->images()->build(__DIR__ . '/app/', ['t' => 'new-test-image']);

// delete image
$client->images()->delete('new-test-image');

