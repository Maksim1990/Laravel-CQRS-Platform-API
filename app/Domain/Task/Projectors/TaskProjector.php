<?php

namespace App\Domain\Task\Projectors;

use App\Domain\Task\Events\TaskCreated;
use App\Domain\Task\Events\TaskDeleted;
use App\Domain\Task\Events\TaskUpdated;
use App\Models\Task;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class TaskProjector extends Projector
{
    public function onTaskCreated(TaskCreated $event, string $aggregateUuid)
    {
        if(!$event->isSaveModel()) {
            return;
        }
        Task::create(array_merge(['uuid' => $aggregateUuid], $event->getPayload()));
    }

    public function onTaskUpdated(TaskUpdated $event)
    {
        $task = $event->getTask();
        $task->fill($event->getPayload())->save();
    }

    public function onTaskDeleted(TaskDeleted $event, string $aggregateUuid)
    {
        Task::uuid($aggregateUuid)->delete();
    }
}
