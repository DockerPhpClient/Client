<?php
declare(strict_types=1);

namespace Docker\Client\Exception;

use Docker\Client\Model\ImageBuildLog;
use Exception;

class ImageBuildException extends Exception
{
    private ?ImageBuildLog $buildLog;

    /**
     * ImageBuildException constructor.
     * @param string|null $message
     * @param ImageBuildLog|null $buildLog
     */
    public function __construct(?string $message = "", ImageBuildLog $buildLog = null)
    {
        $this->buildLog = $buildLog;
        parent::__construct($message, 500);
    }

    public function getBuildLog(): ?ImageBuildLog
    {
        return $this->buildLog;
    }
}