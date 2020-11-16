<?php


namespace Docker\Client\Socket\Handler;


use Exception;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class LogStreamHandler implements StreamHandlerInterface
{
    private LoggerInterface $logger;
    private string $logLevel;

    public function __construct(LoggerInterface $logger, string $logLevel = LogLevel::INFO)
    {
        $this->logger = $logger;
        $this->logLevel = $logLevel;
    }

    public function onData(string $data): string
    {
        $this->logger->log($this->logLevel, $data);
        return $data;
    }

    public function onError(Exception $e): Exception
    {
        $this->logger->error($e->getMessage(), $e->getTrace());
        return $e;
    }

    public function onEnd(string $data): string
    {
        $this->logger->log($this->logLevel, $data);
        return $data;
    }
}