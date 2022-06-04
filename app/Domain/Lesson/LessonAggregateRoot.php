<?php

namespace App\Domain\Lesson;

use App\Domain\BaseAggregateRoot;
use App\Domain\Lesson\Events\LessonCreated;
use App\Domain\Lesson\Events\LessonDeleted;
use App\Domain\Lesson\Events\LessonUpdated;
use App\Models\Lesson;

final class LessonAggregateRoot extends BaseAggregateRoot
{
    public function createLesson(array $attributes, $isSaveModel = true)
    {
        $this->recordThat(new LessonCreated($attributes, $isSaveModel));

        return $this;
    }

    public function applyLessonCreated(LessonCreated $event)
    {
        // var_dump('Lesson added');
    }

    public function deleteLesson()
    {
        $this->recordThat(new LessonDeleted);

        return $this;
    }

    public function applyLessonDeleted(LessonDeleted $event)
    {
        // var_dump('Lesson deleted');
    }

    public function updateLesson(Lesson $lesson, array $attributes)
    {
        $this->recordThat(new LessonUpdated($lesson, $attributes));

        return $this;
    }

    public function applyLessonUpdated(LessonUpdated $event)
    {
         //var_dump('Lesson updated');
    }
}
