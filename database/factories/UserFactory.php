<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;

class UserFactory extends AbstractFactory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'user_system_uuid' => '5f6266417fce6d33aa5583d2',
            'password' => bcrypt(config('system.system_user_pass')),
            'remember_token' => Str::random(10),
        ];
    }
}
