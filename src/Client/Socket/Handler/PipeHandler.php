<?php


namespace Docker\Client\Socket\Handler;


use Exception;

class PipeHandler implements StreamHandlerInterface
{
    /**
     * @var StreamHandlerInterface[]
     */
    private array $handlers;

    /**
     * PipeHandler constructor.
     * @param StreamHandlerInterface[] $handlers
     */
    public function __construct(array $handlers)
    {
        $this->handlers = $handlers;
    }

    public function onData(string $data): string
    {
        foreach ($this->handlers as $handler) {
            $data = $handler->onData($data);
        }
        return $data;
    }

    public function onError(Exception $e): Exception
    {
        foreach ($this->handlers as $handler) {
            $e = $handler->onError($e);
        }
        return $e;
    }

    public function onEnd(string $data): string
    {
        foreach ($this->handlers as $handler) {
            $data = $handler->onEnd($data);
        }
        return $data;
    }
}