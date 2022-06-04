<?php
namespace App\Domain\Section\Events;

use App\Events\BaseEvent;
use App\Models\Section;

final class SectionUpdated extends BaseEvent
{
    /**
     * @var array 
     */
    public array $attributes;

    private Section $section;

    public function __construct(Section $section, array $attributes)
    {
        $this->attributes = $attributes;
        $this->section = $section;

        parent::__construct($attributes);
    }

    public function getSection(): Section
    {
        return $this->section;
    }
}
