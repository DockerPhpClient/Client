<?php


namespace Docker\Client\Manager;


use Docker\OpenAPI\Client;

abstract class AbstractManager
{
    protected Client $apiClient;
    protected array $options;

    public function __construct(Client $apiClient, array $options)
    {
        $this->apiClient = $apiClient;
        $this->options = $options;
    }

}