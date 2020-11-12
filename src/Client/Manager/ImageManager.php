<?php


namespace Docker\Client\Manager;


use Docker\Client\Context\ImageContext;
use Docker\Client\Endpoint\ImageBuild;
use Docker\OpenAPI\Client;
use Docker\OpenAPI\Model\BuildInfo;
use Docker\OpenAPI\Model\ImageDeleteResponseItem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use splitbrain\PHPArchive\ArchiveCorruptedException;
use splitbrain\PHPArchive\ArchiveIllegalCompressionException;
use splitbrain\PHPArchive\ArchiveIOException;
use splitbrain\PHPArchive\FileInfoException;
use splitbrain\PHPArchive\Tar;

class ImageManager
{
    private Client $apiClient;
    private string $fetchType;

    /**
     * @param Client $apiClient
     * @param string $fetchType
     */
    public function __construct(Client $apiClient, string $fetchType = Client::FETCH_OBJECT)
    {
        $this->apiClient = $apiClient;
        $this->fetchType = $fetchType;
    }

    /**
     * @param ImageContext $imageContext
     * @param array $queryParameters
     * @param array $headerParameters
     * @return BuildInfo[]
     * @throws ArchiveCorruptedException
     * @throws ArchiveIOException
     * @throws ArchiveIllegalCompressionException
     * @throws FileInfoException
     */
    public function build(ImageContext $imageContext, array $queryParameters = [], array $headerParameters = []): array
    {
        return $this->apiClient->executeEndpoint(new ImageBuild($imageContext->toStream(), $queryParameters, $headerParameters), Client::FETCH_OBJECT);
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @return ImageDeleteResponseItem[]|ResponseInterface|null
     */
    public function delete(string $idOrName, array $queryParameters = [])
    {
        return $this->apiClient->imageDelete($idOrName, $queryParameters, $this->fetchType);
    }

    /**
     * @param BuildInfo[] $buildInfos
     * @return string[]
     */
    public function toBuildLog(array $buildInfos): array
    {
        return array_map(static function (BuildInfo $buildInfo) {
            return $buildInfo->getStream();
        }, $buildInfos);
    }

    /**
     * @param BuildInfo[] $buildInfos
     * @return string
     */
    public function toBuildId(array $buildInfos): string
    {
        foreach ($buildInfos as $buildInfo) {
            if (null !== $buildInfo->getAux()) {
                return $buildInfo->getAux()->getID();
            }
        }
        return "";
    }
}