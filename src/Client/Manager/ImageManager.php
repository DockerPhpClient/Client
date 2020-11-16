<?php


namespace Docker\Client\Manager;


use Docker\Client\Context\ImageContext;
use Docker\Client\DockerClient;
use Docker\Client\Endpoint\ImageBuild;
use Docker\OpenAPI\Client;
use Docker\OpenAPI\Model\BuildInfo;
use Docker\OpenAPI\Model\ImageDeleteResponseItem;
use Psr\Http\Message\ResponseInterface;
use splitbrain\PHPArchive\ArchiveIOException;

class ImageManager extends AbstractManager
{
    /**
     * @param ImageContext $imageContext
     * @param array $queryParameters
     * @param array $headerParameters
     * @param string $fetch
     * @return ResponseInterface|void|null
     * @throws ArchiveIOException
     */
    public function build(
        ImageContext $imageContext,
        array $queryParameters = [],
        array $headerParameters = [],
        string $fetch = DockerClient::FETCH_STREAM
    ) {
        if (DockerClient::FETCH_STREAM === $fetch) {
            $this->streamClient->imageBuild($imageContext->toStream(), $queryParameters, $headerParameters, Client::FETCH_RESPONSE);
            return;
        }
        if (Client::FETCH_OBJECT === $fetch || Client::FETCH_RESPONSE === $fetch) {
            return $this->socketClient->executeEndpoint(new ImageBuild($imageContext->toStream(), $queryParameters, $headerParameters), $fetch);
        }
    }

    /**
     * @param string $idOrName
     * @param array $queryParameters
     * @return ImageDeleteResponseItem[]|ResponseInterface|null
     */
    public function delete(string $idOrName, array $queryParameters = [])
    {
        return $this->socketClient->imageDelete($idOrName, $queryParameters, Client::FETCH_RESPONSE);
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