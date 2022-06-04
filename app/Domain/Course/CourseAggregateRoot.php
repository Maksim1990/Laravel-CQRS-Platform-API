<?php

namespace App\Domain\Course;

use App\Domain\BaseAggregateRoot;
use App\Domain\Course\Events\CourseCreated;
use App\Domain\Course\Events\CourseDeleted;
use App\Domain\Course\Events\CourseUpdated;
use App\Models\Course;

final class CourseAggregateRoot extends BaseAggregateRoot
{
    public function createCourse(array $attributes, $isSaveModel = true)
    {
        $this->recordThat(new CourseCreated($attributes, $isSaveModel));

        return $this;
    }

    public function applyCourseCreated(CourseCreated $event)
    {
        // var_dump('Course added');
    }

    public function deleteCourse()
    {
        $this->recordThat(new CourseDeleted);

        return $this;
    }

    public function applyCourseDeleted(CourseDeleted $event)
    {
        // var_dump('Course deleted');
    }

    public function updateCourse(Course $course, array $attributes)
    {
        $this->recordThat(new CourseUpdated($course, $attributes));

        return $this;
    }

    public function applyCourseUpdated(CourseUpdated $event)
    {
         //var_dump('Course updated');
    }
}
