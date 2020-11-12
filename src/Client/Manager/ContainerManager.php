<?php


namespace Docker\Client\Manager;


use Docker\OpenAPI\Client;
use Docker\OpenAPI\Model\ContainersCreatePostBody;
use Docker\OpenAPI\Model\ContainerSummaryItem;
use Psr\Http\Message\ResponseInterface;

class ContainerManager
{
    private Client $apiClient;
    private string $fetchType;

    public function __construct(Client $apiClient, string $fetchType = Client::FETCH_OBJECT)
    {
        $this->apiClient = $apiClient;
        $this->fetchType = $fetchType;
    }

    public function create(ContainersCreatePostBody $container, array $queryParameters = [])
    {
        return $this->apiClient->containerCreate($container, $queryParameters, $this->fetchType);
    }

    public function start(string $idOrName, array $queryParameters = []): ?ResponseInterface
    {
        return $this->apiClient->containerStart($idOrName, $queryParameters, $this->fetchType);
    }

    public function delete(string $idOrName, array $queryParameters = []): ?ResponseInterface
    {
        return $this->apiClient->containerDelete($idOrName, $queryParameters, $this->fetchType);
    }

    public function wait(string $idOrName, array $queryParameters = [])
    {
        return $this->apiClient->containerWait($idOrName, $queryParameters, $this->fetchType);
    }

    public function logs(string $idOrName): string
    {
        $response = $this->apiClient->containerAttach($idOrName, ['logs' => true, 'stdout' => true, 'stderr' => true], Client::FETCH_RESPONSE);
        return ($response instanceof ResponseInterface) ? $response->getBody()->getContents() : "";
    }

    /**
     * @param array $queryParameters
     * @return ContainerSummaryItem[]
     */
    public function list(array $queryParameters = []): array
    {
        return $this->apiClient->containerList($queryParameters, $this->fetchType);
    }

    /**
     * TODO: WIP
     * @param ContainerSummaryItem[] $containers
     * @return string[]
     */
    public function toContainerListLog(array $containers): array
    {
        return array_map(static function (ContainerSummaryItem $container) {
            return substr($container->getId(), 8) . " " . $container->getNames()[0] . " " . $container->getImage() . " (" . substr($container->getImageID(), 8) . ")\n";
        }, $containers);
    }
}