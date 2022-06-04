<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="Lesson")
 */
class Lesson
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

    /**
     * @OA\Property(type="integer")
     */
    public $user_id;

}
/**
 *  @OA\Schema(
 *   schema="CreateLesson",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Lesson"),
 *       @OA\Schema(
 *           required={"name","description","course_id"}
 *       )
 *   }
 * )
 */
