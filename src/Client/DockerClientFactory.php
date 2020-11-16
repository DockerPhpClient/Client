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
use Symfony\Component\OptionsResolver\OptionsResolver;

class DockerClientFactory
{
    /**
     * @param array $socketClientOptions
     * @return DockerClient
     */
    public static function create(array $socketClientOptions = []): DockerClient
    {
        $optionsResolver = new OptionsResolver();
        static::configureOptions($optionsResolver);

        $options = $optionsResolver->resolve($socketClientOptions);

        $socketClient = new SocketHttpClient([
            'remote_socket' => $options['remoteSocket']
        ]);
        $host = preg_match('/unix:\/\//', $options['remoteSocket']) ? 'http://localhost' : $options['remoteSocket'];

        $httpClient = new PluginClient($socketClient, [
            #new ErrorPlugin(),
            new ContentLengthPlugin(),
            new DecoderPlugin(),
            new AddHostPlugin(new Uri($host)),
        ]);

        unset($options['remoteSocket']);
        return new DockerClient(ApiClient::create($httpClient), $options);
    }

    protected static function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'remoteSocket' => 'unix:///var/run/docker.sock',
            'registries' => []
        ]);
    }
}