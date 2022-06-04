<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

abstract class AbstractModel extends Model
{
    use HasFactory;

    public static function uuid(string $uuid): ?Model
    {
        return static::where('uuid', $uuid)->first();
    }
}
