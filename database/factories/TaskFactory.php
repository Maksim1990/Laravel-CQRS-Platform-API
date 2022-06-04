<?php

namespace Database\Factories;

use App\Models\Lesson;
use App\Models\Task;
use App\Models\User;
use App\Utils\ModelUtil;
use Illuminate\Support\Str;

class TaskFactory extends AbstractFactory
{
    private const MODEL_BASE_NAME = 'task';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Task::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->title,
            'uuid' => Str::uuid()->toString(),
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
