<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use App\Task;
use App\User;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(Task::class, function (Faker $faker) {
    $user = factory(User::class)->create();
    return [
        'name' => $faker->name,
        'description' => str_random(20),
        'status' => $faker->boolean(),
        'user_id' => $user->id
    ];
});
