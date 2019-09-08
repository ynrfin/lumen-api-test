<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\Checklist::class, function (Faker\Generator $faker) {
    return [
        'object_id' => $faker->randomNumber(),
        'object_domain' => $faker->sentence(3),
        'description' => $faker->paragraph(),
        'is_completed' => $faker->boolean(),
        'due'=> $faker->dateTime(),
        'urgency' => $faker->randomElement([0, 1, 2, 3, 4]),
    ];
});
