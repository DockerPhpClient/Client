<?php

use Docker\Client\Context\ContextFactory;
use Docker\Client\Context\ImageContext;
use Docker\Client\Context\ImageContextFactory;
use Docker\Client\DockerClientFactory;
use splitbrain\PHPArchive\Tar;

require_once __DIR__ . '/../vendor/autoload.php';

$client = DockerClientFactory::create();

// build image
$factory = new ContextFactory(Tar::class);
$context = $factory->createImageContext(__DIR__ . '/app/');
$buildInfos = $client->images()->build($context, ['t' => 'new-test-image']);

// print build log and receive id
echo implode("", $client->images()->toBuildLog($buildInfos));
echo "\nImage ID: " . $client->images()->toBuildId($buildInfos) . "\n";

// delete image
$client->images()->delete('new-test-image');

