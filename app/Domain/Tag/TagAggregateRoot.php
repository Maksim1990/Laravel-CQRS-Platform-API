<?php

namespace App\Domain\Tag;

use App\Domain\BaseAggregateRoot;
use App\Domain\Tag\Events\TagCreated;
use App\Domain\Tag\Events\TagDeleted;
use App\Domain\Tag\Events\TagUpdated;
use App\Models\Tag;
use Illuminate\Support\Facades\DB;

final class TagAggregateRoot extends BaseAggregateRoot
{
    public function createTag(array $attributes, $isSaveModel = true)
    {
        $this->recordThat(new TagCreated($attributes, $isSaveModel));

        return $this;
    }

    public function applyTagCreated(TagCreated $event)
    {
        // var_dump('Tag added');
    }

    public function deleteTag(Tag $tag)
    {
        $this->recordThat(new TagDeleted($tag));

        return $this;
    }

    public function applyTagDeleted(TagDeleted $event)
    {
        // var_dump('Tag deleted');
        DB::table('taggables')->where('tag_id', $event->getTag()->id)->delete();
    }

    public function updateTag(Tag $tag, array $attributes)
    {
        $this->recordThat(new TagUpdated($tag, $attributes));

        return $this;
    }

    public function applyTagUpdated(TagUpdated $event)
    {
         //var_dump('Tag updated');
    }
}
