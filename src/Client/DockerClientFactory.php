<?php
namespace Docker\Client;

use Docker\OpenAPI\Client as ApiClient;
use GuzzleHttp\Psr7\Uri;
use Http\Client\Common\Plugin\AddHostPlugin;
use Http\Client\Common\Plugin\ContentLengthPlugin;
use Http\Client\Common\Plugin\DecoderPlugin;
use Http\Client\Common\Plugin\ErrorPlugin;
use Http\Client\Common\PluginClient;
use Http\Client\Socket\Client as SocketHttpClient;

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

        $socketClient = new SocketHttpClient([
            'remote_socket' => $remoteSocket
        ]);
        $host = preg_match('/unix:\/\//', $remoteSocket) ? 'http://localhost' : $remoteSocket;

        $httpClient = new PluginClient($socketClient, [
            #new ErrorPlugin(),
            new ContentLengthPlugin(),
            new DecoderPlugin(),
            new AddHostPlugin(new Uri($host)),
        ]);

        return new DockerClient(ApiClient::create($httpClient));
    }
}