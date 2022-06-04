<?php

namespace App\Domain\Comment;

use App\Domain\BaseAggregateRoot;
use App\Domain\Comment\Events\CommentCreated;
use App\Domain\Comment\Events\CommentDeleted;
use App\Domain\Comment\Events\CommentUpdated;
use App\Models\Comment;

final class CommentAggregateRoot extends BaseAggregateRoot
{
    public function createComment(array $attributes, $isSaveModel = true)
    {
        $this->recordThat(new CommentCreated($attributes, $isSaveModel));

        return $this;
    }

    public function applyCommentCreated(CommentCreated $event)
    {
        // var_dump('Comment added');
    }

    public function deleteComment()
    {
        $this->recordThat(new CommentDeleted);

        return $this;
    }

    public function applyCommentDeleted(CommentDeleted $event)
    {
        // var_dump('Comment deleted');
    }

    public function updateComment(Comment $comment, array $attributes)
    {
        $this->recordThat(new CommentUpdated($comment, $attributes));

        return $this;
    }

    public function applyCommentUpdated(CommentUpdated $event)
    {
         //var_dump('Comment updated');
    }
}
