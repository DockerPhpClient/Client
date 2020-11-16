<?php
namespace Docker\Client;

use Docker\Client\Socket\Handler\EchoStreamHandler;
use Docker\Client\Socket\Handler\JsonStreamHandler;
use Docker\Client\Socket\Handler\LogStreamHandler;
use Docker\Client\Socket\Handler\PipeHandler;
use Docker\Client\Socket\SocketClient;
use Docker\Client\Socket\SocketStreamClient;
use Docker\OpenAPI\Client as ApiClient;
use GuzzleHttp\Psr7\Uri;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;

class DockerClientFactory
{
    /**
     * @param array $socketClientOptions
     * @return DockerClient
     */
    public static function create(array $socketClientOptions = []): DockerClient
    {
        $remoteSocket = array_key_exists('remote_socket', $socketClientOptions)
            ? $socketClientOptions['remote_socket']
            : 'unix:///var/run/docker.sock';
        $host = preg_match('/unix:\/\//', $remoteSocket) ? 'http://localhost' : $remoteSocket;

        $extraPlugins = [];
        if (array_key_exists('enableErrorPlugin', $socketClientOptions) && $socketClientOptions['enableErrorPlugin'] === true) {
            $extraPlugins[] = new ErrorPlugin();
        }

        $socketClient = self::createSocketClient($remoteSocket, $host, $extraPlugins);
        $streamClient = self::createStreamClient($remoteSocket, $host, $extraPlugins);

        $socketClient = ApiClient::create($socketClient);
        $streamClient = ApiClient::create($streamClient);

        return new DockerClient($socketClient, $streamClient);
    }

    /**
     * @param string $remoteSocket
     * @param string $host
     * @param array $extraPlugins
     * @return PluginClient
     */
    private static function createStreamClient(string $remoteSocket, string $host, array $extraPlugins = []): PluginClient
    {
        $streamClient = new SocketStreamClient($remoteSocket);
        $streamClient->setStreamHandler(new PipeHandler([
            //new JsonStreamHandler(true),
            new EchoStreamHandler(),
        ]));

        $plugins = [
            new ContentLengthPlugin(),
            new AddHostPlugin(new Uri($host)),
        ];

        return new PluginClient($streamClient, array_merge($plugins, $extraPlugins));
    }

    private static function createSocketClient(string $remoteSocket, string $host, array $extraPlugins = []): PluginClient
    {
        $socketClient = new SocketClient($remoteSocket);

        $plugins = [
            new ContentLengthPlugin(),
            new DecoderPlugin(),
            new AddHostPlugin(new Uri($host)),
        ];

        return new PluginClient($socketClient, array_merge($plugins, $extraPlugins));
    }
}