<?php

namespace App\Domain\Lesson\Events;

use App\Events\BaseEvent;

final class LessonCreated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    public function __construct(array $attributes, bool $isSaveModel = true)
    {
        $this->attributes = $attributes;

        parent::__construct($attributes, $isSaveModel);
    }
}
