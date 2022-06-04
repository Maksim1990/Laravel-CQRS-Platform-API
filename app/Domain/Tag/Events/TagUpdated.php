<?php
namespace App\Domain\Tag\Events;

use App\Events\BaseEvent;
use App\Models\Tag;

final class TagUpdated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    private Tag $tag;

    public function __construct(Tag $tag, array $attributes)
    {
        $this->attributes = $attributes;
        $this->tag = $tag;

        parent::__construct($attributes);
    }

    public function getTag(): Tag
    {
        return $this->tag;
    }
}
