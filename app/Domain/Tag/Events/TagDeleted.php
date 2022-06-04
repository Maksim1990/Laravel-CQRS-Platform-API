<?php

namespace App\Domain\Tag\Events;

use App\Events\BaseEvent;
use App\Models\Tag;

final class TagDeleted extends BaseEvent
{
    private Tag $tag;

    public function __construct(Tag $tag)
    {
        $this->tag = $tag;

        parent::__construct($tag->toArray());
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }
}
