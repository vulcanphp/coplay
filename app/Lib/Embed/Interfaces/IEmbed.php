<?php

namespace App\Lib\Embed\Interfaces;

/**
 * Interface for Embed classes.
 *
 * Any class that implements this interface should provide methods to get the
 * links for the embed player and the auto update object.
 * 
 * @package App\Lib\Embed\Interfaces
 */
interface IEmbed
{
    /**
     * Get the links for the embed player.
     *
     * @return array Links for the embed player.
     */
    public function getLinks(): array;

    /**
     * Get the auto update object.
     *
     * @return IAutoUpdate Auto update object.
     */
    public function getUpdater(): IAutoUpdate;
}

