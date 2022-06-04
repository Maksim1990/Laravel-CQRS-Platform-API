<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="Video")
 */
class Video
{
    /**
     * @OA\Property(type="string")
     */
    public $title;

    /**
     * @OA\Property(type="string")
     */
    public $description;

    /**
     * @OA\Property(type="string")
     */
    public $link;

    /**
     * @OA\Property(type="integer")
     */
    public $lesson_id;

    /**
     * @OA\Property(type="integer")
     */
    public $user_id;

}
/**
 *  @OA\Schema(
 *   schema="CreateVideo",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Video"),
 *       @OA\Schema(
 *           required={"title","description","link","lesson_id"}
 *       )
 *   }
 * )
 */
