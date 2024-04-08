<?php

namespace VulcanPhp\FileSystem\Handler;

use VulcanPhp\FileSystem\Exceptions\ImageException;
use VulcanPhp\FileSystem\Interfaces\IImageHandler;

class ImageHandler implements IImageHandler
{
    protected $image, $source, $info;

    public function __construct(?string $imageSource = null)
    {
        if (!extension_loaded('gd')) {
            throw new ImageException('Extension: GD is required to create image');
        }

        // check all the available function exists
        foreach ([
            'getimagesize', 'imagecreatefromjpeg', 'imagecreatefrompng', 'imagejpeg',
            'imagecreatetruecolor', 'imagecopyresampled', 'imagecreatefromgif'
        ] as $func) {
            if (!function_exists($func)) {
                throw new ImageException('Required function: ' . $func . '() is not found.');
            }
        }

        if ($imageSource !== null) {
            $this->setSource($imageSource);
        }
    }

    public function setSource(string $source): self
    {
        if (!file_exists($source)) {
            throw new ImageException('image file:' . $source . ' does not exists..');
        }

        $this->image  = null;
        $this->source = $source;
        $this->info   = array_merge(getimagesize($source), pathinfo($source));

        return $this;
    }

    public function getSource()
    {
        if (!isset($this->source)) {
            throw new ImageException('Source does not specified');
        }

        return $this->source;
    }

    public function getInfo(?string $key = null)
    {
        if (!isset($this->info)) {
            throw new ImageException('Image does not set');
        }

        return $key !== null ? ($this->info[$key] ?? null) : $this->info;
    }

    public function getImage()
    {
        if (!isset($this->image)) {
            // Create a new image from file
            switch ($this->getInfo('mime')) {
                case 'image/jpeg':
                case 'image/jpg':
                    $this->image = imagecreatefromjpeg($this->getSource());
                    break;
                case 'image/png':
                    $this->image = imagecreatefrompng($this->getSource());
                    break;
                case 'image/gif':
                    $this->image = imagecreatefromgif($this->getSource());
                    break;
                default:
                    throw new ImageException('Unsupported image extension for:' . $this->getSource());
            }
        }

        return $this->image;
    }

    // Compress 75%
    public function compress(int $quality = 75, $destination = null): bool
    {
        if ($destination === null) {
            $destination = $this->getSource();
        }

        $image = $this->getImage();

        if (file_exists($destination)) {
            unlink($destination);
        }

        if ($this->getInfo('mime') == 'image/png') {
            imagesavealpha($image, true);
            return imagepng($image, $destination, $quality);
        } else {
            return imagejpeg($image, $destination, $quality);
        }
    }

    public function resize(int $img_width, int $img_height, ?string $destination = null): bool
    {
        if ($destination === null) {
            $destination = $this->getSource();
        }

        list($width, $height) = $this->getInfo();

        $image      = $this->getImage();
        $aspect     = $width / $height;
        $img_aspect = $img_width / $img_height;

        if ($aspect >= $img_aspect) {
            // If image is wider than thumbnail (in aspect ratio sense)
            $new_height = $img_height;
            $new_width  = $width / ($height / $img_height);
        } else {
            // If the thumbnail is wider than the image
            $new_width  = $img_width;
            $new_height = $height / ($width / $img_width);
        }

        if ($this->getInfo('mime') == 'image/png') {
            imagesavealpha($image, true);
        }

        $photo = imagecreatetruecolor($img_width, $img_height);

        if ($this->getInfo('mime') == 'image/png') {
            imagealphablending($photo, false);
            imagesavealpha($photo, true);

            $transparent = imagecolorallocatealpha($photo, 255, 255, 255, 127);
            imagefilledrectangle($photo, 0, 0, intval($new_width), intval($new_height), $transparent);
        }

        if (file_exists($destination)) {
            unlink($destination);
        }

        imagecopyresampled(
            $photo,
            $image,
            intval(0 - ($new_width - $img_width) / 2),
            intval(0 - ($new_height - $img_height) / 2),
            0,
            0,
            intval($new_width),
            intval($new_height),
            intval($width),
            intval($height)
        );

        if ($this->getInfo('mime') == 'image/png') {
            return imagepng($photo, $destination);
        } else {
            return imagejpeg($photo, $destination);
        }
    }

    public function bulkResize(array $sizes): array
    {
        $saved = [];
        foreach ($sizes as $width => $height) {
            $savePath = sprintf('%s/%s-%sx%s.%s', $this->getInfo('dirname'), $this->getInfo('filename'), $width, $height, $this->getInfo('extension'));
            if ($this->resize($width, $height, $savePath)) {
                $saved[] = $savePath;
            }
        }

        return $saved;
    }

    public function rotate(float $degrees): bool
    {
        $image = $this->getImage();

        if ($this->getInfo('mime') == 'image/png') {
            imagesavealpha($image, true);
        }

        // Rotate
        $rotate = imagerotate($image, $degrees, 0);

        //and save it on your server...
        if ($this->getInfo('mime') == 'image/png') {
            return imagepng($rotate, $this->getSource());
        } else {
            return imagejpeg($rotate, $this->getSource());
        }
    }
}
