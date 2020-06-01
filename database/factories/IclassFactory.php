<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Iclass;
use Faker\Generator as Faker;

$factory->define(Iclass::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'description' => $faker->sentence
    ];
});
