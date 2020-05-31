<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Admin;
use Faker\Generator as Faker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$factory->define(Admin::class, function (Faker $faker) {
    return [
        'institute_id' => 1,
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'country_code' => '91',
        'phone' => $faker->numberBetween(0000000000, 9999999999),
        'password' => Hash::make('welcome'),
        'remember_token' => Str::random(10),
        'status' => 1,
    ];
});
