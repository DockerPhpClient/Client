<?php

namespace Docker\Client\Context;

use Docker\Client\Stream\TarStream;
use FilesystemIterator;
use GuzzleHttp\Psr7\Utils;
use Psr\Http\Message\StreamInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use splitbrain\PHPArchive\Archive;
use splitbrain\PHPArchive\ArchiveCorruptedException;
use splitbrain\PHPArchive\ArchiveIllegalCompressionException;
use splitbrain\PHPArchive\ArchiveIOException;
use splitbrain\PHPArchive\FileInfoException;
use splitbrain\PHPArchive\Tar;

class ImageContext
{
    protected const FLAGS =
        FilesystemIterator::KEY_AS_PATHNAME |
        FilesystemIterator::CURRENT_AS_FILEINFO |
        FilesystemIterator::SKIP_DOTS;

    private string $contextDirectory;
    private Archive $archive;

    public function __construct($contextDirectory, Archive $archive)
    {
        $this->contextDirectory = $contextDirectory;
        $this->archive = $archive;
    }

    /**
     * @return StreamInterface
     * @throws ArchiveIOException
     */
    public function toStream(): StreamInterface
    {
        $this->archive->create();

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($this->contextDirectory, static::FLAGS)) as $file) {
            if ($file->isFile()) {
                $this->archive->addFile($file->getPathname(), str_replace($this->contextDirectory, '', $file->getPathname()));
            }
        }

        return Utils::streamFor($this->archive->getArchive());
    }
}