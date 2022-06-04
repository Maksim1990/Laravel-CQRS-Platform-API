<?php
namespace Database\Factories;

use App\Models\Lesson;
use App\Models\User;
use App\Models\Video;
use App\Utils\ModelUtil;
use Illuminate\Support\Str;

class VideoFactory extends AbstractFactory
{
    private const MODEL_BASE_NAME = 'video';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->text,
            'uuid' => Str::uuid()->toString(),
            'link' => $this->faker->url,
            'user_id' => ModelUtil::getRandomModelId(User::class),
            'lesson_id' => ModelUtil::getRandomModelId(Lesson::class),
            'description' => $this->faker->realText(300),
        ];
    }

    public function configure()
    {
        return $this->generateModelEvents(self::MODEL_BASE_NAME);
    }
}
