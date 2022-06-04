<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="Section")
 */
class Section
{
    /**
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * @OA\Property(type="string")
     */
    public $description;

    /**
     * @OA\Property(type="integer")
     */
    public $course_id;

}
/**
 *  @OA\Schema(
 *   schema="CreateSection",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Section"),
 *       @OA\Schema(
 *           required={"name","description","course_id"}
 *       )
 *   }
 * )
 */
