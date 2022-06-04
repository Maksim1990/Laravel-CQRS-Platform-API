<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="System")
 */
class System
{
    /**
     * @OA\Property(type="string")
     */
    public $version;
}

