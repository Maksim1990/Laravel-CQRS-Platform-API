<?php

namespace App\Domain\Section;

use App\Domain\BaseAggregateRoot;
use App\Domain\Section\Events\SectionCreated;
use App\Domain\Section\Events\SectionDeleted;
use App\Domain\Section\Events\SectionUpdated;
use App\Models\Section;

final class SectionAggregateRoot extends BaseAggregateRoot
{
    public function createSection(array $attributes, $isSaveModel = true)
    {
        $this->recordThat(new SectionCreated($attributes, $isSaveModel));

        return $this;
    }

    public function applySectionCreated(SectionCreated $event)
    {
        // var_dump('Section added');
    }

    public function deleteSection()
    {
        $this->recordThat(new SectionDeleted);

        return $this;
    }

    public function applySectionDeleted(SectionDeleted $event)
    {
        // var_dump('Section deleted');
    }

    public function updateSection(Section $section, array $attributes)
    {
        $this->recordThat(new SectionUpdated($section, $attributes));

        return $this;
    }

    public function applySectionUpdated(SectionUpdated $event)
    {
         //var_dump('Section updated');
    }
}
