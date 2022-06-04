<?php

namespace App\Domain\Tag\Projectors;

use App\Domain\Tag\Events\TagCreated;
use App\Domain\Tag\Events\TagDeleted;
use App\Domain\Tag\Events\TagUpdated;
use App\Models\Tag;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class TagProjector extends Projector
{
    public function onTagCreated(TagCreated $event, string $aggregateUuid)
    {
        if(!$event->isSaveModel()) {
            return;
        }
        Tag::create(array_merge(['uuid' => $aggregateUuid], $event->getPayload()));
    }

    public function onTagUpdated(TagUpdated $event)
    {
        $tag = $event->getTag();
        $tag->fill($event->getPayload())->save();
    }

    public function onTagDeleted(TagDeleted $event, string $aggregateUuid)
    {
        Tag::uuid($aggregateUuid)->delete();
    }
}
