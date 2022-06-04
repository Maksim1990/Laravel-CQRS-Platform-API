<?php
namespace Database\Factories;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use App\Utils\ModelUtil;
use Illuminate\Support\Str;

class LessonFactory extends AbstractFactory
{
    private const MODEL_BASE_NAME = 'lesson';

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Lesson::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->company,
            'uuid' => Str::uuid()->toString(),
            'user_id' => ModelUtil::getRandomModelId(User::class),
            'course_id' => ModelUtil::getRandomModelId(Course::class),
            'section_id' => ModelUtil::getRandomModelId(Section::class),
            'description' => $this->faker->realText(300),
        ];
    }

    public function configure()
    {
        return $this->generateModelEvents(self::MODEL_BASE_NAME);
    }
}
