<?php
namespace App\Services;

use App\Mappings\TagMapping;
use App\Models\AbstractModel;
use App\Models\Course;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;


class TagsManager
{
    private const TAG_MORPHABLES_TABLE = 'taggables';

    public static function processModelTags(
        AbstractModel $model,
        Request $request
    ) {
        $tags = $request->get('tags');
        $action = $request->get('action') ?? TagMapping::ATTACH_ACTION;

        if ($action === TagMapping::ATTACH_ACTION) {
            self::attachModelTags($model, $tags);
        } else {
            self::detachModelTags($model, $tags);
        }
    }

    public static function attachModelTags(AbstractModel $model, array $tags): AbstractModel
    {
        $tags = Tag::find(array_diff($tags, $model->tags()->pluck('id')->toArray()));

        if (strtolower(class_basename($model)) === 'course') {
            self::attachCourseTags($model, $tags);
            return $model;
        }

        $model->tags()->attach($tags);
        return $model;
    }

    public static function detachModelTags(AbstractModel $model, array $tagIds): AbstractModel
    {
        $tags = Tag::find($tagIds);

        if (strtolower(class_basename($model)) === 'course') {
            self::detachCourseTags($model, $tags);
            return $model;
        }

        $model->tags()->detach($tags);
        return $model;
    }

    private static function attachCourseTags(Course $course, Collection $tags): void
    {
        $taggablesData = [];

        foreach ($tags as $tag) {
            $taggablesData[] = [
                'tag_id' => $tag->id,
                'taggable_id' => $course->id,
                'taggable_type' => get_class($course),
            ];
        }

        if ($taggablesData) {
            DB::table(self::TAG_MORPHABLES_TABLE)->insert($taggablesData);
        }
    }

    private static function detachCourseTags(Course $course, Collection $tags): void
    {
        foreach ($tags as $tag) {
            DB::table(self::TAG_MORPHABLES_TABLE)
                ->where('tag_id', $tag->id)
                ->where('taggable_id', $course->id)
                ->where('taggable_type', get_class($course))
                ->delete();
        }
    }
}
