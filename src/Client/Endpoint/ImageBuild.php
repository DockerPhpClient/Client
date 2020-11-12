<?php


namespace Docker\Client\Endpoint;

use Docker\OpenAPI\Endpoint\ImageBuild as BaseEndpoint;
use Docker\OpenAPI\Model\BuildInfo;

class ImageBuild extends BaseEndpoint
{
    /**
     * {@inheritDoc}
     */
    protected function transformResponseBody(string $body, int $status, \Symfony\Component\Serializer\SerializerInterface $serializer, ?string $contentType)
    {
        if (200 === $status) {
            $data = $this->logToJson($body);
            return $serializer->deserialize($data, BuildInfo::class . '[]', 'json');
        }
        return parent::transformResponseBody($body, $status, $serializer, $contentType);
    }

    /**
     * @param string $body
     * @return string
     */
    protected function logToJson(string $body): string
    {
        $data = explode("\n", $body);

        // remove empty line(s)
        $data = array_filter($data, static function ($line) {
            return null !== $line && !empty($line);
        });

        // rebuild as json array
        $data = "[" . implode(",", $data) . "]";
        return $data;
    }
}