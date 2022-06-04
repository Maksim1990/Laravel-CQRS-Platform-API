<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="Comment")
 */
class Comment
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
 *   schema="CreateComment",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Comment"),
 *       @OA\Schema(
 *           required={"name","type","model_id"}
 *       )
 *   }
 * )
 */
