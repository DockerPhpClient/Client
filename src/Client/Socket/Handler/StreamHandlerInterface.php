<?php

namespace Docker\Client\Socket\Handler;

use Exception;

interface StreamHandlerInterface
{
    public function onData(string $data): string;
    public function onError(Exception $e): Exception;
    public function onEnd(string $data): string;
}