<?php
namespace Docker\Client;

use Docker\Client\Manager\ContainerManager;
use Docker\OpenAPI\Client as ApiClient;

class DockerClient
{
    private ApiClient $apiClient;

    private ?ContainerManager $containerManager = null;

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

}