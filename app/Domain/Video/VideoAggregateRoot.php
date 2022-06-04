<?php

namespace App\Domain\Video;

use App\Domain\BaseAggregateRoot;
use App\Domain\Video\Events\VideoCreated;
use App\Domain\Video\Events\VideoDeleted;
use App\Domain\Video\Events\VideoUpdated;
use App\Models\Video;

final class VideoAggregateRoot extends BaseAggregateRoot
{
    public function createVideo(array $attributes, $isSaveModel = true)
    {
        $this->recordThat(new VideoCreated($attributes, $isSaveModel));

        return $this;
    }

    public function applyVideoCreated(VideoCreated $event)
    {
        // var_dump('Video added');
    }

    public function deleteVideo()
    {
        $this->recordThat(new VideoDeleted);

        return $this;
    }

    public function applyVideoDeleted(VideoDeleted $event)
    {
        // var_dump('Course deleted');
    }

    public function updateVideo(Video $video, array $attributes)
    {
        $this->recordThat(new VideoUpdated($video, $attributes));

        return $this;
    }

    public function applyVideoUpdated(VideoUpdated $event)
    {
         //var_dump('Video updated');
    }
}
