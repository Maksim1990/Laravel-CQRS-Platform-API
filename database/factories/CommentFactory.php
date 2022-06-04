<?php

namespace Database\Factories;

use App\Models\Comment;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Task;
use App\Models\User;
use App\Models\Video;
use App\Utils\ModelUtil;
use Illuminate\Support\Str;

class CommentFactory extends AbstractFactory
{
    private const MODEL_BASE_NAME = 'comment';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        [$commentableId, $commentableType] = $this->getPolymorphicModelIdAndType([
            Lesson::class,
            Course::class,
            Task::class,
            Video::class,
        ]);

        return [
            'message' => $this->faker->text,
            'uuid' => Str::uuid()->toString(),
            'user_id' => ModelUtil::getRandomModelId(User::class),
            'commentable_type' => $commentableType,
            'commentable_id' => $commentableId,
        ];
    }

    public function configure()
    {
        return $this->generateModelEvents(self::MODEL_BASE_NAME);
    }
}
