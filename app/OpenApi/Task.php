<?php

namespace App\OpenApi;

/**
 * @OA\Schema(schema="Task")
 */
class Task
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
 *   schema="CreateTask",
 *   type="object",
 *   allOf={
 *       @OA\Schema(ref="#/components/schemas/Task"),
 *       @OA\Schema(
 *           required={"title","description","lesson_id"}
 *       )
 *   }
 * )
 */
