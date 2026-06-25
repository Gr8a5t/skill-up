<?php

namespace App\Services;

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;
use Exception;

class CloudinaryService
{
    /**
     * Initialize Cloudinary SDK Configuration.
     */
    public function __construct()
    {
        $config = config('services.cloudinary');

        if (!empty($config['url'])) {
            Configuration::instance($config['url']);
        } else {
            $cloudinaryUrl = sprintf(
                'cloudinary://%s:%s@%s?secure=true',
                $config['api_key'],
                $config['api_secret'],
                $config['cloud_name']
            );
            Configuration::instance($cloudinaryUrl);
        }
    }

    /**
     * Upload a file to Cloudinary.
     *
     * @param mixed $file Can be a file path, file object, or base64 data URI
     * @param string $folder The destination folder in Cloudinary
     * @param array $options Additional upload options
     * @return string The secure URL of the uploaded image
     * @throws Exception
     */
    public function upload($file, string $folder = 'avatars', array $options = []): string
    {
        $uploadApi = new UploadApi();
        
        $defaultOptions = [
            'folder' => $folder,
        ];

        // Merge options
        $mergedOptions = array_merge($defaultOptions, $options);

        // Perform upload
        $result = $uploadApi->upload($file, $mergedOptions);

        if (!isset($result['secure_url'])) {
            throw new Exception('Failed to upload image to Cloudinary. Response: ' . json_encode($result));
        }

        return $result['secure_url'];
    }
}
