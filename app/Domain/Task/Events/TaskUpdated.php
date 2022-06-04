<?php
namespace App\Domain\Task\Events;

use App\Events\BaseEvent;
use App\Models\Task;

final class TaskUpdated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    private Task $task;

    public function __construct(Task $task, array $attributes)
    {
        $this->attributes = $attributes;
        $this->task = $task;

        parent::__construct($attributes);
    }

    public function getTask(): Task
    {
        return $this->task;
    }
}
