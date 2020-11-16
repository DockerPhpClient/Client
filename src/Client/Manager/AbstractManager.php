<?php


namespace Docker\Client\Manager;


use Docker\OpenAPI\Client as ApiClient;

class AbstractManager
{
    protected ApiClient $socketClient;
    protected ApiClient $streamClient;

    public function __construct(ApiClient $socketClient, ApiClient $streamClient)
    {
        $this->socketClient = $socketClient;
        $this->streamClient = $streamClient;
    }
}