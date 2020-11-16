<?php


namespace Docker\Client\Socket;

use Clue\React\Block;
use Docker\Client\Socket\Exception\RequestException;
use Exception;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Browser;
use React\Promise\PromiseInterface;
use React\Socket\FixedUriConnector;
use React\Socket\UnixConnector;

abstract class AbstractSocketClient implements ClientInterface
{
    protected LoopInterface $loop;
    protected Browser $socket;

    public function __construct($remoteSocket = 'unix:///var/run/docker.sock')
    {
        $this->loop = Factory::create();
        $connector = new FixedUriConnector(
            $remoteSocket,
            new UnixConnector($this->loop)
        );
        $this->socket = new Browser($this->loop, $connector);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws RequestException
     */
    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        try {
            return Block\await($this->sendAsyncRequest($request), $this->loop);
        } catch (Exception $e) {
            throw new RequestException($e->getMessage(), $request, $e);
        }
    }

    abstract public function sendAsyncRequest(RequestInterface $request): PromiseInterface;
}