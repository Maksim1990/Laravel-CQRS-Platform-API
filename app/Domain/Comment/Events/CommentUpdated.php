<?php
namespace App\Domain\Comment\Events;

use App\Events\BaseEvent;
use App\Models\Comment;

final class CommentUpdated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    private Comment $comment;

    public function __construct(Comment $comment, array $attributes)
    {
        $this->attributes = $attributes;
        $this->comment = $comment;

        parent::__construct($attributes);
    }

    public function getComment(): Comment
    {
        return $this->comment;
    }
}
