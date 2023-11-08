<?php

/**
 * A Captcha builder
 */

declare(strict_types=1);

namespace Gregwar\Captcha;

interface CaptchaBuilderInterface
{
    /**
     * Builds the code
     */
    public function build(?int $width, ?int $height, ?string $font, ?string $fingerprint);

    /**
     * Saves the code to a file
     */
    public function save($filename, $quality);

    /**
     * Gets the image contents
     */
    public function get($quality);

    /**
     * Outputs the image
     */
    public function output($quality);
}
