<?php

namespace Docker\Client\Model;


use Docker\OpenAPI\Model\BuildInfo;

class ImageBuildLog
{
    private ?string $id;
    private ?string $error;
    private array $stream;

    /**
     * BuildLog constructor.
     * @param BuildInfo[] $buildInfos
     */
    public function __construct(array $buildInfos)
    {
        $this->id = null;
        $this->error = null;
        $this->stream = [];

        $this->parseBuildInfos($buildInfos);
    }

    public function getId(): ?string
    {
        return $this->id;
    }

    public function hasError(): bool
    {
        return null !== $this->error;
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return string[]
     */
    public function getLogs(): array
    {
        return $this->stream;
    }

    /**
     * @param BuildInfo[] $buildInfos
     */
    private function parseBuildInfos(array $buildInfos): void
    {
        foreach ($buildInfos as $buildInfo) {
            if (null !== $buildInfo->getError()) {
                $this->error = $buildInfo->getError();
            }

            if (null !== $buildInfo->getStream()) {
                $this->stream[] = $buildInfo->getStream();
            }

            if (null !== $buildInfo->getAux() && null !== $buildInfo->getAux()->getID())
            {
                $this->id = $buildInfo->getAux()->getID();
            }
        }
    }
}