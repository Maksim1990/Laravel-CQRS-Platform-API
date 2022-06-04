<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="Tag")
 */
class Tag
{
    /**
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * @OA\Property(type="string")
     */
    public $type;

    /**
     * @OA\Property(type="integer")
     */
    public $model_id;

    /**
     * @OA\Property(type="integer")
     */
    public $user_id;

}
/**
 *  @OA\Schema(
 *   schema="CreateTag",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Tag"),
 *       @OA\Schema(
 *           required={"name","type","model_id"}
 *       )
 *   }
 * )
 */
