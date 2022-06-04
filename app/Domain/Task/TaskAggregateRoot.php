<?php

namespace App\Domain\Task;

use App\Domain\BaseAggregateRoot;
use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskDeleted;
use App\Domain\Task\Events\TaskUpdated;
use App\Models\Task;

final class TaskAggregateRoot extends BaseAggregateRoot
{
    public function createTask(array $attributes, $isSaveModel = true)
    {
        $this->recordThat(new TaskCreated($attributes, $isSaveModel));

        return $this;
    }

    public function applyTaskCreated(TaskCreated $event)
    {
        // var_dump('Task added');
    }

    public function deleteTask()
    {
        $this->recordThat(new TaskDeleted);

        return $this;
    }

    public function applyTaskDeleted(TaskDeleted $event)
    {
        // var_dump('Task deleted');
    }

    public function updateTask(Task $task, array $attributes)
    {
        $this->recordThat(new TaskUpdated($task, $attributes));

        return $this;
    }

    public function applyTaskUpdated(TaskUpdated $event)
    {
         //var_dump('Task updated');
    }
}
