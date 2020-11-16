<?php
declare(strict_types=1);

namespace Docker\Client\Manager;


use Docker\Client\Stream\DockerRawStream;
use Docker\OpenAPI\Client;
use Docker\OpenAPI\Exception\ContainerCreateBadRequestException;
use Docker\OpenAPI\Exception\ContainerCreateConflictException;
use Docker\OpenAPI\Exception\ContainerCreateInternalServerErrorException;
use Docker\OpenAPI\Exception\ContainerCreateNotFoundException;
use Docker\OpenAPI\Exception\ContainerDeleteBadRequestException;
use Docker\OpenAPI\Exception\ContainerDeleteConflictException;
use Docker\OpenAPI\Exception\ContainerDeleteInternalServerErrorException;
use Docker\OpenAPI\Exception\ContainerDeleteNotFoundException;
use Docker\OpenAPI\Exception\ContainerInspectInternalServerErrorException;
use Docker\OpenAPI\Exception\ContainerInspectNotFoundException;
use Docker\OpenAPI\Exception\ContainerStartInternalServerErrorException;
use Docker\OpenAPI\Exception\ContainerStartNotFoundException;
use Docker\OpenAPI\Exception\ContainerWaitInternalServerErrorException;
use Docker\OpenAPI\Exception\ContainerWaitNotFoundException;
use Docker\OpenAPI\Model\ContainersCreatePostBody;
use Docker\OpenAPI\Model\ContainersCreatePostResponse201;
use Docker\OpenAPI\Model\ContainersIdJsonGetResponse200;
use Docker\OpenAPI\Model\ContainersIdWaitPostResponse200;
use Docker\OpenAPI\Model\ContainerSummaryItem;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class ContainerManager extends AbstractManager
{
    /**
     * @param ContainersCreatePostBody $container
     * @param array $queryParameters
     * @return ContainersCreatePostResponse201
     * @throws ContainerCreateBadRequestException
     * @throws ContainerCreateNotFoundException
     * @throws ContainerCreateConflictException
     * @throws ContainerCreateInternalServerErrorException
     */
    public function create(ContainersCreatePostBody $container, array $queryParameters = []): ContainersCreatePostResponse201
    {
        return $this->apiClient->containerCreate($container, $queryParameters, Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @throws ContainerStartNotFoundException
     * @throws ContainerStartInternalServerErrorException
     */
    public function start(string $idOrName, array $queryParameters = []): void
    {
        $this->apiClient->containerStart($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @throws ContainerDeleteBadRequestException
     * @throws ContainerDeleteNotFoundException
     * @throws ContainerDeleteConflictException
     * @throws ContainerDeleteInternalServerErrorException
     */
    public function delete(string $idOrName, array $queryParameters = []): void
    {
        $this->apiClient->containerDelete($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @return ContainersIdWaitPostResponse200
     * @throws ContainerWaitNotFoundException
     * @throws ContainerWaitInternalServerErrorException
     */
    public function wait(string $idOrName, array $queryParameters = []): ContainersIdWaitPostResponse200
    {
        return $this->apiClient->containerWait($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @return DockerRawStream
     */
    public function logs(string $idOrName): DockerRawStream
    {
        $response = $this->apiClient->containerAttach($idOrName, ['logs' => true, 'stdout' => true, 'stderr' => true], Client::FETCH_RESPONSE);
        return new DockerRawStream($response->getBody());
    }

    /**
     * @param string $idOrName
     * @param LoggerInterface $logger
     * @param string $logLevel
     * @param string $errorLogLevel
     */
    public function log(string $idOrName, LoggerInterface $logger, string $logLevel = LogLevel::INFO, $errorLogLevel = LogLevel::ERROR): void
    {
        $stream = $this->logs($idOrName);
        $stream->onStdout(static function ($data) use ($logger, $logLevel) {
            $logger->log($logLevel, $data);
        });
        $stream->onStderr(static function ($data) use ($logger, $errorLogLevel) {
            $logger->log($errorLogLevel, $data);
        });
        $stream->wait();
    }

    /**
     * @param array $queryParameters
     * @return ContainerSummaryItem[]
     */
    public function list(array $queryParameters = []): array
    {
        return $this->apiClient->containerList($queryParameters, Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @return ContainersIdJsonGetResponse200|ResponseInterface
     * @throws ContainerInspectNotFoundException
     * @throws ContainerInspectInternalServerErrorException
     */
    public function inspect(string $idOrName, array $queryParameters = [])
    {
        return $this->apiClient->containerInspect($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @return bool
     */
    public function exists(string $idOrName, array $queryParameters = []): bool
    {
        $response = $this->apiClient->containerInspect($idOrName, $queryParameters, Client::FETCH_RESPONSE);
        return 200 === $response;
    }
}