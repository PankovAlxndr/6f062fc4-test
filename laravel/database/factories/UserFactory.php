<?php

namespace Database\Factories;

use App\Models\Group;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'remember_token' => Str::random(10),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'avatar' => '/avatars/'.$this->faker->md5().'.png',
            'description' => $this->faker->text(),
            'telegram_login' => $this->faker->unique->word(),
            'telegram_id' => $this->faker->unique()->randomNumber(),
            'group_id' => Group::factory(),
        ];
    }
}
