<?php

namespace App\Models;

use VulcanPhp\SimpleDb\Model;

class Links extends Model
{
    public static function tableName(): string
    {
        return 'links';
    }

    public static function primaryKey(): string
    {
        return 'id';
    }

    public static function fillable(): array
    {
        return ['tmdb', 'season', 'episode', 'server', 'link'];
    }
}
