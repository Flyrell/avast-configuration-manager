<?php

namespace App\Service;

use App\Exception\FileException;
use Symfony\Component\Filesystem\Filesystem;
use function file_get_contents;

/**
 * Service used for file content manipulation.
 */
class FileService
{

    public function __construct(private Filesystem $filesystem) {}

    /**
     * Reads file content in the specified $filepath
     *
     * @param string $filepath
     * @return string
     * @throws FileException
     */
    public function read(string $filepath): string
    {
        if (!$this->filesystem->exists($filepath)) {
            throw new FileException(FileException::FILE_NOT_FOUND);
        }

        $content = $this->getFileContents($filepath);
        if ($content === false) {
            throw new FileException(FileException::FILE_UNREADABLE);
        }

        return trim($content);
    }

    /**
     * Reads the file and returns its contents
     *
     * @param string $filepath
     * @return string|bool
     */
    private function getFileContents(string $filepath): string|bool
    {
        return file_get_contents($filepath);
    }
}
