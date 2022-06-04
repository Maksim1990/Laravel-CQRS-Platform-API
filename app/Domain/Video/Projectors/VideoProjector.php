<?php

namespace App\Domain\Video\Projectors;

use App\Domain\Video\Events\VideoCreated;
use App\Domain\Video\Events\VideoDeleted;
use App\Domain\Video\Events\VideoUpdated;
use App\Models\Video;
use Spatie\EventSourcing\EventHandlers\Projectors\Projector;

final class VideoProjector extends Projector
{
    public function onVideoCreated(VideoCreated $event, string $aggregateUuid)
    {
        if(!$event->isSaveModel()) {
            return;
        }
        Video::create(array_merge(['uuid' => $aggregateUuid], $event->getPayload()));
    }

    public function onVideoUpdated(VideoUpdated $event)
    {
        $video = $event->getVideo();
        $video->fill($event->getPayload())->save();
    }

    public function onVideoDeleted(VideoDeleted $event, string $aggregateUuid)
    {
        Video::uuid($aggregateUuid)->delete();
    }
}
