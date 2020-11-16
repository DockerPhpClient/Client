<?php


namespace Docker\Client\Socket\Handler;


use Exception;

class JsonStreamHandler implements StreamHandlerInterface
{
    private bool $debug;

    public function __construct(bool $debug = false)
    {
        $this->debug = $debug;
    }

    public function onData(string $data): string
    {
        var_dump($data);
        var_dump(json_decode($data, true));
        if ($this->isJson($data)) {
            $data = json_decode($data, true);

            return $data['stream'];
        }
        return '';
    }

    public function onError(Exception $e): Exception
    {
        return $e;
    }

    public function onEnd(string $data): string
    {
        return $data;
    }

    private function isJson(string $json): bool
    {
        json_decode($json, true);
        return (JSON_ERROR_NONE === json_last_error());
    }

    /**
     * @param string|null $data
     */
    private function printDebug(?string $data): void
    {
        if ($this->debug && null !== $data) {
            echo $data;
        }
    }
}