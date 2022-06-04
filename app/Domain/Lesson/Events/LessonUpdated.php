<?php
namespace App\Domain\Lesson\Events;

use App\Events\BaseEvent;
use App\Models\Lesson;

final class LessonUpdated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    private Lesson $lesson;

    public function __construct(Lesson $lesson, array $attributes)
    {
        $this->attributes = $attributes;
        $this->lesson = $lesson;

        parent::__construct($attributes);
    }

    public function getLesson(): Lesson
    {
        return $this->lesson;
    }
}
