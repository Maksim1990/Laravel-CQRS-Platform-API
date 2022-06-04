<?php

namespace App\Domain\Course\Projectors;

use App\Domain\Course\Events\CourseCreated;
use App\Domain\Course\Events\CourseDeleted;
use App\Domain\Course\Events\CourseUpdated;
use App\Models\Course;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class CourseProjector extends Projector
{
    public function onCourseCreated(CourseCreated $event, string $aggregateUuid): void
    {
        if(!$event->isSaveModel()) {
            return;
        }
        Course::create(array_merge(['uuid' => $aggregateUuid], $event->getPayload()));
    }

    public function onCourseUpdated(CourseUpdated $event)
    {
        $course = $event->getCourse();
        $course->fill($event->getPayload())->save();
    }

    public function onCourseDeleted(CourseDeleted $event, string $aggregateUuid)
    {
        Course::uuid($aggregateUuid)->delete();
    }
}
