<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="Course")
 */
class Course
{
    /**
     * @OA\Property(type="string")
     */
    public $name;

    /**
     * @OA\Property(type="string")
     */
    public $slug;

    /**
     * @OA\Property(type="string")
     */
    public $description;
}
/**
 *  @OA\Schema(
 *   schema="CreateCourse",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Course"),
 *       @OA\Schema(
 *           required={"name","slug"}
 *       )
 *   }
 * )
 */
