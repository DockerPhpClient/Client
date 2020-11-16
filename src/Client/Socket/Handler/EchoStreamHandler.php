<?php


namespace Docker\Client\Socket\Handler;


use Exception;

class EchoStreamHandler implements StreamHandlerInterface
{

    public function onData(string $data): string
    {
        echo $data;
        return $data;
    }

    public function onError(Exception $e): Exception
    {
        echo $e->getMessage();
        return $e;
    }

    public function onEnd(string $data): string
    {
        echo $data;
        return $data;
    }
}