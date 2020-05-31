<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Superadmin;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Superadmin::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'password' => $faker->password(6, 20),
        'remember_token' => Str::random(10),
        'status' => 1,
    ];
});
