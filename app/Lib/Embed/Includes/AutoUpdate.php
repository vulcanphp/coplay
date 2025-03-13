<?php

namespace Lib\Embed\Includes;

use Exception;
use Hyper\Utils\Ping;
use Lib\Embed\Interfaces\IAutoUpdate;
use Throwable;

/**
 * Class AutoUpdate
 *
 * This class handles the auto-update mechanism for embedding content.
 * It implements the IAutoUpdate interface and provides methods to check
 * if an update is needed and to perform the update process.
 * 
 * @package Lib\Embed\Includes
 */
class AutoUpdate implements IAutoUpdate
{
    /**
     * AutoUpdate constructor.
     *
     * @param string $mode The mode for the expiration, supported values are 'weekly', 'monthly', 'yearly'
     */
    public function __construct(protected string $mode = 'monthly')
    {
    }

    /**
     * Get the location of the schema file.
     *
     * @return string The location of the schema file
     */
    public function getSchemaLocation(): string
    {
        return storage_dir('temp/embed_schema.json');
    }

    /**
     * Checks if the schema file is expired or does not exist and updates it from the GitHub repository.
     *
     * @param bool $force Whether to force the update regardless of the expiration
     * @return void
     */
    public function check(bool $force = false): void
    {
        // Check if the file does not exist or if it expired
        if ($force || !file_exists($this->getSchemaLocation()) || $this->isExpired()) {
            try {
                // Get the latest schema from GitHub
                $response = Ping::get(
                    'https://raw.githubusercontent.com/vulcanphp/coplay/main/storage/temp/embed_schema.json'
                );

                // Decode the JSON response
                $json = json_decode($response['body'] ?? '', true);

                // Check if the response was successful and if the JSON is valid
                if ($response['status'] === 200 && !empty($json)) {
                    // Save the new schema to the file
                    file_put_contents(
                        $this->getSchemaLocation(),
                        $json
                    );
                } else {
                    // If the response was not successful, throw an exception
                    throw new Exception('Failed to update schema');
                }
            } catch (Throwable $e) {
                // If an exception was thrown, touch the file to prevent repeated updates
                if (file_exists($this->getSchemaLocation())) {
                    touch($this->getSchemaLocation());
                }
            }
        }
    }

    /**
     * Checks if the schema file is expired.
     *
     * @return bool True if the schema is expired, false otherwise
     */
    public function isExpired(): bool
    {
        // Determine the duration from the mode
        $durations = [
            'daily' => '-1 day',
            'weekly' => '-1 week',
            'b-weekly' => '-14 days',
            'monthly' => '-1 month',
            'quarterly' => '-3 months',
        ];

        // Get the duration based on the mode
        $duration = $durations[strtolower($this->mode)] ?? '-3 months';

        // Check if the file's last modification time is less than the duration
        return filemtime($this->getSchemaLocation()) < strtotime($duration);
    }
}
