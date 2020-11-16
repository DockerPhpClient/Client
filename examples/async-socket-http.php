<?php

use Psr\Http\Message\ResponseInterface;
use React\Http\Browser;
use RingCentral\Psr7;

require_once __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$connector = new React\Socket\FixedUriConnector(
    'unix:///var/run/docker.sock',
    new React\Socket\UnixConnector($loop)
);

$browser = new Browser($loop, $connector);

//$browser->post('http://localhost/containers/repeat/attach?logs=1&stdout=1')->then(function (Psr\Http\Message\ResponseInterface $response) {
//    var_dump($response->getHeaders(), (string)$response->getBody());
//});

$browser->requestStreaming('POST', 'http://localhost/containers/repeat/attach?logs=1&stdout=1')->then(function (Psr\Http\Message\ResponseInterface $response) {
    $body = $response->getBody();
    assert($body instanceof Psr\Http\Message\StreamInterface);
    assert($body instanceof React\Stream\ReadableStreamInterface);

    $body->on('data', function ($chunk) {
        echo $chunk;
    });

    $body->on('error', function (Exception $error) {
        echo 'Error: ' . $error->getMessage() . PHP_EOL;
    });

    $body->on('close', function () {
        echo '[DONE]' . PHP_EOL;
    });
});

//$browser->get('http://localhost/info')->then(function (ResponseInterface $response) {
//    echo Psr7\str($response);
//}, 'printf');

$loop->run();