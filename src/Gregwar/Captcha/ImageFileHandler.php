<?php

/**
 * Handles actions related to captcha image files including saving and garbage collection
 *
 * @author Gregwar <g.passault@gmail.com>
 * @author Jeremy Livingston <jeremy@quizzle.com>
 */

declare(strict_types=1);

namespace Gregwar\Captcha;

use Symfony\Component\Finder\Finder;

class ImageFileHandler
{
    /**
     * Name of folder for captcha images
     * @var string
     */
    protected $imageFolder;

    /**
     * Absolute path to public web folder
     * @var string
     */
    protected $webPath;

    /**
     * Frequency of garbage collection in fractions of 1
     * @var int
     */
    protected $gcFreq;

    /**
     * Maximum age of images in minutes
     * @var int
     */
    protected $expiration;

    /**
     * @param $imageFolder
     * @param $webPath
     * @param $gcFreq
     * @param $expiration
     */
    public function __construct($imageFolder, $webPath, $gcFreq, $expiration)
    {
        $this->imageFolder      = $imageFolder;
        $this->webPath          = $webPath;
        $this->gcFreq           = $gcFreq;
        $this->expiration       = $expiration;
    }

    /**
     * Saves the provided image content as a file
     *
     * @param \GdImage $contents
     *
     * @return string
     */
    public function saveAsFile(\GdImage $contents): string
    {
        $this->createFolderIfMissing();

        $filename = md5(uniqid('', true)) . '.jpg';
        $filePath = $this->webPath . '/' . $this->imageFolder . '/' . $filename;
        imagejpeg($contents, $filePath, 15);

        return '/' . $this->imageFolder . '/' . $filename;
    }

    /**
     * Randomly runs garbage collection on the image directory
     *
     * @return bool
     */
    public function collectGarbage()
    {
        if (!random_int(1, $this->gcFreq) === 1) {
            return false;
        }

        $this->createFolderIfMissing();

        $finder = new Finder();
        $criteria = sprintf('<= now - %s minutes', $this->expiration);
        $finder->in($this->webPath . '/' . $this->imageFolder)->date($criteria);

        foreach ($finder->files() as $file) {
            unlink($file->getPathname());
        }

        return true;
    }

    /**
     * Creates the folder if it doesn't exist
     */
    protected function createFolderIfMissing(): void
    {
        if (
            !file_exists($this->webPath . '/' . $this->imageFolder)
            && !mkdir($concurrentDirectory = $this->webPath . '/' . $this->imageFolder, 0755)
            && !is_dir($concurrentDirectory)
        ) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }
}
