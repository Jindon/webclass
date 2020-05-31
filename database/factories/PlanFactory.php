<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Plan;
use Faker\Generator as Faker;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'status' => true,
        'max_students' => $faker->numberBetween(100, 3000),
        'max_uploads' => $faker->numberBetween(100, 500),
        'price' => $faker->numberBetween(30000, 100000),
    ];
});
