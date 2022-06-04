<?php

namespace App\Domain\Comment\Projectors;

use App\Domain\Comment\Events\CommentCreated;
use App\Domain\Comment\Events\CommentDeleted;
use App\Domain\Comment\Events\CommentUpdated;
use App\Models\Comment;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class CommentProjector extends Projector
{
    public function onCommentCreated(CommentCreated $event, string $aggregateUuid)
    {
        if(!$event->isSaveModel()) {
            return;
        }
        Comment::create(array_merge(['uuid' => $aggregateUuid], $event->getPayload()));
    }

    public function onCommentUpdated(CommentUpdated $event)
    {
        $comment = $event->getComment();
        $comment->fill($event->getPayload())->save();
    }

    public function onCommentDeleted(CommentDeleted $event, string $aggregateUuid)
    {
        Comment::uuid($aggregateUuid)->delete();
    }
}
