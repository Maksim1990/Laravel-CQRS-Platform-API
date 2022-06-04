<?php

namespace App\Domain\Section\Projectors;

use App\Domain\Section\Events\SectionCreated;
use App\Domain\Section\Events\SectionDeleted;
use App\Domain\Section\Events\SectionUpdated;
use App\Models\Section;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class SectionProjector extends Projector
{
    public function onSectionCreated(SectionCreated $event, string $aggregateUuid)
    {
        if(!$event->isSaveModel()) {
            return;
        }
        Section::create(array_merge(['uuid' => $aggregateUuid], $event->getPayload()));
    }

    public function onSectionUpdated(SectionUpdated $event)
    {
        $section = $event->getSection();
        $section->fill($event->getPayload())->save();
    }

    public function onSectionDeleted(SectionDeleted $event, string $aggregateUuid)
    {
        Section::uuid($aggregateUuid)->delete();
    }
}
