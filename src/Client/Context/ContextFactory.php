<?php /** @noinspection PhpMissingFieldTypeInspection */

namespace Docker\Client\Context;

class ContextFactory
{
    /**
     * @var class-string<Archive> $archiveClass
     */
    private $archiveClass;

    /**
     * ImageContextFactory constructor.
     * @param class-string<Archive> $archiveClass
     */
    public function __construct($archiveClass)
    {
        $this->archiveClass = $archiveClass;
    }

    public function createImageContext(string $contextDirectory): ImageContext
    {
        return new ImageContext($contextDirectory, new $this->archiveClass());
    }
}