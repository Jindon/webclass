<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Institute;
use Faker\Generator as Faker;

$factory->define(Institute::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'board' => $faker->word,
        'logo' => null,
        'status' => true,
    ];
});
