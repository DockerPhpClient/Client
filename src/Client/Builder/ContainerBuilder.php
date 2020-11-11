<?php
namespace Docker\Client\Builder;

use Docker\OpenAPI\Model\ContainersCreatePostBody;
use Docker\OpenAPI\Model\HostConfig;

class ContainerBuilder
{
    private ContainersCreatePostBody $container;
    private HostConfig $hostConfig;

    public function __construct()
    {
        $this->container = new ContainersCreatePostBody();
        $this->hostConfig = new HostConfig();
    }

    public function fromImage(string $imageName): self
    {
        $this->container->setImage($imageName);
        return $this;
    }

    public function addBind(string $hostPath, string $containerPath, string $mode = 'rw'): self
    {
        $binds = $this->hostConfig->getBinds();
        $binds[] = "${hostPath}:${containerPath}:${mode}";
        $this->hostConfig->setBinds($binds);

        return $this;
    }

    public function entryPoint(?array $entrypoint = []): self
    {
        $this->container->setEntrypoint($entrypoint);
        return $this;
    }

    public function build(): ContainersCreatePostBody
    {
        $this->container->setHostConfig($this->hostConfig);
        return $this->container;
    }
}