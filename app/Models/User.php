<?php

namespace App\Models;

use Spark\Database\Model;

/**
 * Class User
 * 
 * This class represents the User model. It extends the
 * base Model class from the Spark framework.
 * 
 * @package App\Models
 */
class User extends Model
{
    public static string $table = 'users';

    protected array $guarded = [];
}

