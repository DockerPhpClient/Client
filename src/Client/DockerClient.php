<?php
namespace Docker\Client;

use Docker\Client\Manager\ContainerManager;
use Docker\Client\Manager\ImageManager;
use Docker\Client\Socket\SocketClient as SocketClient;
use Docker\Client\Stream\StreamClosure;
use Docker\OpenAPI\Client;
use Docker\OpenAPI\Client as ApiClient;
use Docker\OpenAPI\Runtime\Client\Endpoint;
use Symfony\Component\Serializer\SerializerInterface;

class DockerClient
{
    public const FETCH_RESPONSE = Client::FETCH_RESPONSE;
    public const FETCH_OBJECT = Client::FETCH_OBJECT;
    public const FETCH_STREAM = 'stream';

    private ApiClient $socketClient;
    private ApiClient $streamClient;

    private ?ContainerManager $containerManager = null;
    private ?ImageManager $imageManager = null;

    public function __construct(ApiClient $socketClient, ApiClient $streamClient)
    {
        $this->socketClient = $socketClient;
        $this->streamClient = $streamClient;
    }

    public function containers(): ContainerManager
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->socketClient, $this->streamClient);
        }
        return $this->containerManager;
    }

    public function images(): ImageManager
    {
        if (null === $this->imageManager) {
            $this->imageManager = new ImageManager($this->socketClient, $this->streamClient);
        }
        return $this->imageManager;
    }
}