<?php
use Faker\Generator as Faker;

$factory->define(Scaupize1123\JustOfficalNews\NewsCategory::class, function (Faker $faker) {
    return [
       'status' => 1,
       'created_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
       'updated_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
    ];
});
