<?php

require_once __DIR__ . '/../vendor/autoload.php';

$loop = React\EventLoop\Factory::create();
$connector = new React\Socket\UnixConnector($loop);

$message = 'POST /containers/repeat/attach?logs=1&stdout=1 HTTP/1.1'."\r\nHost: localhost\r\n\r\n";

$connector->connect('/var/run/docker.sock')->then(function (React\Socket\ConnectionInterface $connection) use ($loop, $message) {
    $connection->pipe(new React\Stream\WritableResourceStream(STDOUT, $loop));
    $connection->write($message);
    $connection->write("\r\n\r\n");
    $connection->end();
});

$loop->run();



