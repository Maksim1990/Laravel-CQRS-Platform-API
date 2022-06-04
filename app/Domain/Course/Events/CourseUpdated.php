<?php
namespace App\Domain\Course\Events;

use App\Events\BaseEvent;
use App\Models\Course;

final class CourseUpdated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    private Course $course;

    public function __construct(Course $course, array $attributes)
    {
        $this->attributes = $attributes;
        $this->course = $course;

        parent::__construct($attributes);
    }

    public function getCourse(): Course
    {
        return $this->course;
    }
}
