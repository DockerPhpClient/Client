<?php
namespace Docker\Client\Socket;

use Closure;
use Clue\React\Block;
use Docker\Client\Socket\Handler\StreamHandlerInterface;
use Evenement\EventEmitterInterface;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use React\Promise\Deferred;
use React\Promise\PromiseInterface;
use React\Promise\PromisorInterface;
use React\Stream\ReadableResourceStream;
use React\Stream\ReadableStreamInterface;

class SocketStreamClient extends AbstractSocketClient
{
    private ?StreamHandlerInterface $streamHandler = null;

    public function setStreamHandler(StreamHandlerInterface $handler): self
    {
        $this->streamHandler = $handler;
        return $this;
    }

    public function sendRequest(RequestInterface $request): ResponseInterface
    {
        return Block\await($this->sendAsyncRequest($request)->then(function(ResponseInterface $response) {
            /** @var ReadableStreamInterface $body */
            $body = $response->getBody();

            if (null !== $this->streamHandler) {
                $body->on('data', Closure::fromCallable([$this->streamHandler, 'onData']));
                $body->on('error', Closure::fromCallable([$this->streamHandler, 'onError']));
            }

            $waitForStreamEnding = $this->setUpStreamEndingPromise($body);
            $this->loop->run();

            Block\await($waitForStreamEnding, $this->loop);

            return $response;
        }), $this->loop);
    }

    public function sendAsyncRequest(RequestInterface $request): PromiseInterface
    {
        return $this->socket->requestStreaming($request->getMethod(), $request->getUri(), $request->getHeaders(), $request->getBody());
    }

    private function setUpStreamEndingPromise(EventEmitterInterface $stream): PromiseInterface
    {
        $deferred = new Deferred();

        $stream->on('end', function () use ($deferred) {
            $deferred->resolve();
        });

        $stream->on('error', function (Exception $e) use ($deferred) {
            $deferred->reject($e);
        });

        return $deferred->promise();
    }
}