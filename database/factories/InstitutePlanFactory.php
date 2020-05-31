<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\InstitutePlan;
use Faker\Generator as Faker;

$factory->define(InstitutePlan::class, function (Faker $faker) {
    return [
        'institute_id' => 1,
        'plan_id' => 1,
        'start_date' => \Illuminate\Support\Carbon::now()->toDate(),
        'end_date' => \Illuminate\Support\Carbon::now()->addYear()->toDate(),
        'status' => true,
    ];
});
