<?php

namespace App\Core\Embed\Interfaces;

interface IEmbed
{
    public function getLinks(): array;

    public function getUpdater(): IAutoUpdate;
}
