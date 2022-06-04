<?php
namespace App\Mappings;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Task;
use App\Models\Video;

class ModelMapping
{
    public const COURSE_MODEL_DISPLAYED = 'COURSE';
    public const LESSON_MODEL_DISPLAYED = 'LESSON';
    public const TASK_MODEL_DISPLAYED = 'TASK';
    public const VIDEO_MODEL_DISPLAYED = 'VIDEO';

    public const COURSE_MODEL = Course::class;
    public const LESSON_MODEL = Lesson::class;
    public const TASK_MODEL = Task::class;
    public const VIDEO_MODEL = Video::class;

    public const MODELS_LIST =[
        self::COURSE_MODEL => self::COURSE_MODEL_DISPLAYED,
        self::LESSON_MODEL => self::LESSON_MODEL_DISPLAYED,
        self::TASK_MODEL => self::TASK_MODEL_DISPLAYED,
        self::VIDEO_MODEL => self::VIDEO_MODEL_DISPLAYED,
    ];
}
