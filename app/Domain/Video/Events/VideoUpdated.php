<?php
namespace App\Domain\Video\Events;

use App\Events\BaseEvent;
use App\Models\Video;

final class VideoUpdated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    private Video $video;

    public function __construct(Video $video, array $attributes)
    {
        $this->attributes = $attributes;
        $this->video = $video;

        parent::__construct($attributes);
    }

    public function getVideo(): Video
    {
        return $this->video;
    }
}
