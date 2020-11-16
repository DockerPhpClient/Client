<?php


namespace Docker\Client\Manager;


use Docker\OpenAPI\Client;
use Docker\OpenAPI\Model\ContainersCreatePostBody;
use Psr\Http\Message\ResponseInterface;

class ContainerManager extends AbstractManager
{
    public function create(ContainersCreatePostBody $container, array $queryParameters = [])
    {
        return $this->socketClient->containerCreate($container, $queryParameters, Client::FETCH_OBJECT);
    }

    public function start(string $idOrName, array $queryParameters = []): ?ResponseInterface
    {
        return $this->socketClient->containerStart($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }

    public function delete(string $idOrName, array $queryParameters = []): ?ResponseInterface
    {
        return $this->socketClient->containerDelete($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }

    public function wait(string $idOrName, array $queryParameters = [])
    {
        return $this->socketClient->containerWait($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }

    public function logs(string $idOrName, bool $stream = false): string
    {
        $response = $this->streamClient->containerAttach($idOrName, ['logs' => true, 'stream' => $stream, 'stdout' => true, 'stderr' => true], Client::FETCH_RESPONSE);
        return ($response instanceof ResponseInterface) ? $response->getBody()->getContents() : "";
    }

    public function list(array $queryParameters = []): array
    {
        return $this->socketClient->containerList($queryParameters, Client::FETCH_OBJECT);
    }
}