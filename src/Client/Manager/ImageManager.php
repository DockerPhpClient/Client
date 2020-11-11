<?php


namespace Docker\Client\Manager;


use Docker\Client\Context\ImageContext;
use Docker\Client\Stream\TarStream;
use Docker\OpenAPI\Client;
use Docker\OpenAPI\Model\ContainersCreatePostBody;
use GuzzleHttp\Psr7\Stream;
use Http\Client\Exception;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class ImageManager
{
    private Client $apiClient;
    private string $fetchType;

    public function __construct(Client $apiClient, string $fetchType = Client::FETCH_OBJECT)
    {
        $this->apiClient = $apiClient;
        $this->fetchType = $fetchType;
    }

    public function build(string $contextDirectory, array $queryParameters = [], array $headerParameters = []): ?ResponseInterface
    {
        $context = new ImageContext($contextDirectory);
        $stream = $context->getStream();

        return $this->apiClient->imageBuild($stream, $queryParameters, $headerParameters, Client::FETCH_RESPONSE);
    }

    public function delete(string $idOrName, array $queryParameters = [])
    {
        return $this->apiClient->imageDelete($idOrName, $queryParameters, $this->fetchType);
    }


}