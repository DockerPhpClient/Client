<?php
declare(strict_types=1);

namespace Docker\Client\Manager;

use Docker\Client\Context\ImageContext;
use Docker\Client\Endpoint\ImageBuild;
use Docker\Client\Exception\ImageBuildException;
use Docker\Client\Model\ImageBuildLog;
use Docker\OpenAPI\Client;
use Docker\OpenAPI\Exception\ImageBuildBadRequestException;
use Docker\OpenAPI\Exception\ImageBuildInternalServerErrorException;
use Docker\OpenAPI\Exception\ImageInspectInternalServerErrorException;
use Docker\OpenAPI\Exception\ImageInspectNotFoundException;
use Docker\OpenAPI\Model\BuildInfo;
use Docker\OpenAPI\Model\Image;
use Docker\OpenAPI\Model\ImageDeleteResponseItem;
use Psr\Http\Message\ResponseInterface;
use splitbrain\PHPArchive\ArchiveIOException;

class ImageManager
{
    private Client $apiClient;

    public function __construct(Client $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @param ImageContext $imageContext
     * @param array $queryParameters
     * @param array $headerParameters
     * @return ImageBuildLog
     * @throws ArchiveIOException
     * @throws ImageBuildException
     * @throws ImageBuildBadRequestException
     * @throws ImageBuildInternalServerErrorException
     */
    public function build(ImageContext $imageContext, array $queryParameters = [], array $headerParameters = []): ImageBuildLog
    {
        /** @var BuildInfo[] $buildInfos */
        $buildInfos = $this->apiClient->executeEndpoint(new ImageBuild($imageContext->toStream(), $queryParameters, $headerParameters), Client::FETCH_OBJECT);

        $imageBuildLog = new ImageBuildLog($buildInfos);
        if ($imageBuildLog->hasError()) {
            throw new ImageBuildException($imageBuildLog->getError(), $imageBuildLog);
        }

        return $imageBuildLog;
    }

    /**
     * @param string $idOrName
     * @return Image
     * @throws ImageInspectNotFoundException
     * @throws ImageInspectInternalServerErrorException
     */
    public function inspect(string $idOrName): Image
    {
        return $this->apiClient->imageInspect($idOrName, Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @return bool
     */
    public function exists(string $idOrName): bool
    {
        $response = $this->apiClient->imageInspect($idOrName, Client::FETCH_RESPONSE);
        return 200 === $response->getStatusCode();
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @return ImageDeleteResponseItem[]
     */
    public function delete(string $idOrName, array $queryParameters = []): array
    {
        return $this->apiClient->imageDelete($idOrName, $queryParameters, Client::FETCH_OBJECT);
    }
}