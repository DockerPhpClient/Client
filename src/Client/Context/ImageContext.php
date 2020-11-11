<?php

namespace Docker\Client\Context;

use Docker\Client\Stream\TarStream;
use Psr\Http\Message\StreamInterface;

class ImageContext
{
    private string $directory;
    /**
     * @var resource $process
     */
    private $process;

    /**
     * @var resource $stream
     */
    private $stream;

    public function __construct(string $directory)
    {
        $this->directory = $directory;
    }

    public function getStream(): StreamInterface
    {
        $this->process = proc_open("/usr/bin/env tar c .", [["pipe", "r"], ["pipe", "w"], ["pipe", "w"]], $pipes, $this->directory);
        $this->stream = $pipes[1];

        return new TarStream($this->stream);
    }

    public function __destruct()
    {
        if (is_resource($this->process)) {
            proc_close($this->process);
        }

        if (is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

}