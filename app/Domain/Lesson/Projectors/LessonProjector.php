<?php

namespace App\Domain\Lesson\Projectors;

use App\Domain\Lesson\Events\LessonCreated;
use App\Domain\Lesson\Events\LessonDeleted;
use App\Domain\Lesson\Events\LessonUpdated;
use App\Models\Lesson;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class LessonProjector extends Projector
{
    public function onLessonCreated(LessonCreated $event, string $aggregateUuid)
    {
        if(!$event->isSaveModel()) {
            return;
        }
        Lesson::create(array_merge(['uuid' => $aggregateUuid], $event->getPayload()));
    }

    public function onLessonUpdated(LessonUpdated $event)
    {
        $lesson = $event->getLesson();
        $lesson->fill($event->getPayload())->save();
    }

    public function onLessonDeleted(LessonDeleted $event, string $aggregateUuid)
    {
        Lesson::uuid($aggregateUuid)->delete();
    }
}
