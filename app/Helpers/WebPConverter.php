<?php

namespace App\Helpers;

class WebPConverter
{
    private $quality = 80;
    private $supportedFormats = ['jpg', 'jpeg', 'png', 'gif'];

    public function __construct($quality = 80)
    {
        $this->quality = max(1, min(100, $quality));
    }

    /**
     * Convert image to WebP format
     *
     * @param string $sourcePath Path to source image
     * @param string $destinationPath Optional destination path (if not provided, replaces original)
     * @param bool $deleteOriginal Whether to delete original file after conversion
     * @return string|false Path to converted image or false on failure
     */
    public function convert($sourcePath, $destinationPath = null, $deleteOriginal = true)
    {
        if (!$this->isWebPSupported()) {
            log_message('error', 'WebP is not supported on this server');
            return false;
        }

        if (!file_exists($sourcePath)) {
            log_message('error', 'Source image file does not exist: ' . $sourcePath);
            return false;
        }

        $pathInfo = pathinfo($sourcePath);
        $extension = strtolower($pathInfo['extension']);

        // If already WebP, return as is
        if ($extension === 'webp') {
            return $sourcePath;
        }

        // Check if format is supported
        if (!in_array($extension, $this->supportedFormats)) {
            log_message('error', 'Unsupported image format: ' . $extension);
            return false;
        }

        // Set destination path if not provided
        if ($destinationPath === null) {
            $destinationPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
        }

        try {
            $sourceImage = $this->createImageResource($sourcePath, $extension);
            
            if ($sourceImage === false) {
                log_message('error', 'Failed to create image resource from: ' . $sourcePath);
                return false;
            }

            // Convert to WebP
            $success = imagewebp($sourceImage, $destinationPath, $this->quality);
            imagedestroy($sourceImage);

            if ($success) {
                // Delete original if requested and conversion successful
                if ($deleteOriginal && $sourcePath !== $destinationPath) {
                    unlink($sourcePath);
                }
                return $destinationPath;
            } else {
                log_message('error', 'Failed to convert image to WebP: ' . $sourcePath);
                return false;
            }
        } catch (Exception $e) {
            log_message('error', 'Error converting image to WebP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Convert multiple images to WebP
     *
     * @param array $imagePaths Array of image paths
     * @param bool $deleteOriginals Whether to delete original files
     * @return array Array of conversion results
     */
    public function convertMultiple($imagePaths, $deleteOriginals = true)
    {
        $results = [];
        
        foreach ($imagePaths as $imagePath) {
            $result = $this->convert($imagePath, null, $deleteOriginals);
            $results[$imagePath] = $result;
        }
        
        return $results;
    }

    /**
     * Convert directory of images to WebP
     *
     * @param string $directoryPath Path to directory containing images
     * @param bool $recursive Whether to process subdirectories
     * @param bool $deleteOriginals Whether to delete original files
     * @return array Array of conversion results
     */
    public function convertDirectory($directoryPath, $recursive = false, $deleteOriginals = true)
    {
        if (!is_dir($directoryPath)) {
            log_message('error', 'Directory does not exist: ' . $directoryPath);
            return [];
        }

        $results = [];
        $iterator = $recursive ? 
            new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directoryPath)) :
            new \DirectoryIterator($directoryPath);

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $extension = strtolower($file->getExtension());
                if (in_array($extension, $this->supportedFormats)) {
                    $result = $this->convert($file->getPathname(), null, $deleteOriginals);
                    $results[$file->getPathname()] = $result;
                }
            }
        }

        return $results;
    }

    /**
     * Convert uploaded file to WebP
     *
     * @param object $file CodeIgniter file object
     * @param string $uploadPath Path where to save the converted file
     * @return string|false Path to converted file or false on failure
     */
    public function convertUploadedFile($file, $uploadPath)
    {
        if (!$file->isValid() || $file->hasMoved()) {
            return false;
        }

        $extension = strtolower($file->getExtension());
        
        // Generate unique filename
        $fileName = uniqid() . '_' . time() . '.webp';
        $destinationPath = rtrim($uploadPath, '/') . '/' . $fileName;

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // If already WebP, just move it
        if ($extension === 'webp') {
            if ($file->move($uploadPath, $fileName)) {
                return $destinationPath;
            }
            return false;
        }

        // Move to temporary location first
        $tempPath = $uploadPath . 'temp_' . $file->getName();
        if (!$file->move(dirname($tempPath), basename($tempPath))) {
            return false;
        }

        // Convert to WebP
        $result = $this->convert($tempPath, $destinationPath, true);
        
        return $result;
    }

    /**
     * Get WebP version of an image URL/path
     *
     * @param string $imagePath Original image path
     * @return string WebP version path
     */
    public function getWebPPath($imagePath)
    {
        $pathInfo = pathinfo($imagePath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
    }

    /**
     * Check if WebP format is supported
     *
     * @return bool
     */
    public function isWebPSupported()
    {
        return function_exists('imagewebp') && (imagetypes() & IMG_WEBP);
    }

    /**
     * Get image dimensions
     *
     * @param string $imagePath Path to image
     * @return array|false Array with width and height or false on failure
     */
    public function getImageDimensions($imagePath)
    {
        if (!file_exists($imagePath)) {
            return false;
        }

        $info = getimagesize($imagePath);
        if ($info === false) {
            return false;
        }

        return [
            'width' => $info[0],
            'height' => $info[1],
            'type' => $info[2],
            'mime' => $info['mime']
        ];
    }

    /**
     * Resize image while converting to WebP
     *
     * @param string $sourcePath Source image path
     * @param string $destinationPath Destination path
     * @param int $maxWidth Maximum width
     * @param int $maxHeight Maximum height
     * @param bool $maintainAspectRatio Whether to maintain aspect ratio
     * @return string|false
     */
    public function resizeAndConvert($sourcePath, $destinationPath, $maxWidth, $maxHeight, $maintainAspectRatio = true)
    {
        if (!file_exists($sourcePath)) {
            return false;
        }

        $dimensions = $this->getImageDimensions($sourcePath);
        if (!$dimensions) {
            return false;
        }

        $sourceWidth = $dimensions['width'];
        $sourceHeight = $dimensions['height'];

        // Calculate new dimensions
        if ($maintainAspectRatio) {
            $ratio = min($maxWidth / $sourceWidth, $maxHeight / $sourceHeight);
            $newWidth = intval($sourceWidth * $ratio);
            $newHeight = intval($sourceHeight * $ratio);
        } else {
            $newWidth = $maxWidth;
            $newHeight = $maxHeight;
        }

        try {
            $pathInfo = pathinfo($sourcePath);
            $extension = strtolower($pathInfo['extension']);
            
            $sourceImage = $this->createImageResource($sourcePath, $extension);
            if (!$sourceImage) {
                return false;
            }

            $resizedImage = imagecreatetruecolor($newWidth, $newHeight);
            
            // Preserve transparency for PNG
            if ($extension === 'png') {
                imagealphablending($resizedImage, false);
                imagesavealpha($resizedImage, true);
                $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
                imagefill($resizedImage, 0, 0, $transparent);
            }

            imagecopyresampled(
                $resizedImage, $sourceImage,
                0, 0, 0, 0,
                $newWidth, $newHeight,
                $sourceWidth, $sourceHeight
            );

            $success = imagewebp($resizedImage, $destinationPath, $this->quality);
            
            imagedestroy($sourceImage);
            imagedestroy($resizedImage);

            return $success ? $destinationPath : false;
        } catch (Exception $e) {
            log_message('error', 'Error resizing and converting image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Create image resource from file
     *
     * @param string $imagePath Path to image
     * @param string $extension File extension
     * @return resource|false
     */
    private function createImageResource($imagePath, $extension)
    {
        switch ($extension) {
            case 'jpg':
            case 'jpeg':
                return imagecreatefromjpeg($imagePath);
            case 'png':
                return imagecreatefrompng($imagePath);
            case 'gif':
                return imagecreatefromgif($imagePath);
            case 'webp':
                return imagecreatefromwebp($imagePath);
            default:
                return false;
        }
    }

    /**
     * Set quality for WebP conversion
     *
     * @param int $quality Quality value (1-100)
     */
    public function setQuality($quality)
    {
        $this->quality = max(1, min(100, $quality));
    }

    /**
     * Get current quality setting
     *
     * @return int
     */
    public function getQuality()
    {
        return $this->quality;
    }
}