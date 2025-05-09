<?php

namespace App\Lib\Embed\Interfaces;

/**
 * Interface for auto update.
 * 
 * Any class that implements this interface should provide methods to check for updates.
 * 
 * @package App\Lib\Embed\Interfaces
 */
interface IAutoUpdate
{
    /**
     * Checks if the update is expired.
     *
     * @return bool If the update is expired.
     */
    public function isExpired(): bool;

    /**
     * Checks for updates.
     *
     * @param bool $force If true, update will be forced.
     */
    public function check(bool $force = false): void;
}

