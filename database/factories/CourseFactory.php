<?php
namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use App\Utils\ModelUtil;
use Illuminate\Support\Str;

class CourseFactory extends AbstractFactory
{
    private const MODEL_BASE_NAME = 'course';
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'uuid' => Str::uuid()->toString(),
            'slug' => Str::snake($this->faker->regexify('[a-z]{5,10}-[a-z]{5,10}')),
            'user_id' => ModelUtil::getRandomModelId(User::class),
            'description' => $this->faker->realText(300),
        ];
    }

    public function configure()
    {
        return $this->generateModelEvents(self::MODEL_BASE_NAME);
    }
}
