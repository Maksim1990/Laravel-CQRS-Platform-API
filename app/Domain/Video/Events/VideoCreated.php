<?php

namespace App\Domain\Video\Events;

use App\Events\BaseEvent;

final class VideoCreated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    public function __construct(array $attributes, bool $isSaveModel = true)
    {
        $this->attributes = $attributes;

        parent::__construct($attributes, $isSaveModel);
    }
}
