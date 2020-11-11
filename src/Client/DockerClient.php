<?php
namespace Docker\Client;

use Docker\Client\Manager\ContainerManager;
use Docker\Client\Manager\ImageManager;
use Docker\OpenAPI\Client as ApiClient;

class DockerClient
{
    private ApiClient $apiClient;

    private ?ContainerManager $containerManager = null;
    private ?ImageManager $imageManager = null;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    public function containers(): ContainerManager
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->apiClient);
        }
        return $this->containerManager;
    }

    public function images(): ImageManager
    {
        if (null === $this->imageManager) {
            $this->imageManager = new ImageManager($this->apiClient);
        }
        return $this->imageManager;
    }

}