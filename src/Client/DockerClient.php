<?php
namespace Docker\Client;

use Docker\Client\Manager\ContainerManager;
use Docker\Client\Manager\ImageManager;
use Docker\OpenAPI\Client as ApiClient;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DockerClient
{
    private ApiClient $apiClient;

    private ?ContainerManager $containerManager = null;
    private ?ImageManager $imageManager = null;
    private array $options;

    public function __construct(ApiClient $apiClient, array $options)
    {
        $this->apiClient = $apiClient;
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    public function containers(): ContainerManager
    {
        if (null === $this->containerManager) {
            $this->containerManager = new ContainerManager($this->apiClient, $this->options);
        }
        return $this->containerManager;
    }

    public function images(): ImageManager
    {
        if (null === $this->imageManager) {
            $this->imageManager = new ImageManager($this->apiClient, $this->options);
        }
        return $this->imageManager;
    }

    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'registries' => []
        ]);
    }

}