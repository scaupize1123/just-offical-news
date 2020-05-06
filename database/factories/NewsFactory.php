<?php

use Faker\Generator as Faker;

$factory->define(Scaupize1123\JustOfficalNews\News::class, function (Faker $faker) {
    return [
       'uuid' => $faker->uuid(),
       'status' => 1,
       'news_category_id' => 1,
       'start_date' => $faker->date($format = 'Y-m-d', $max = 'now'),
       'end_date' => $faker->date($format = 'Y-m-d', $max = 'now'),
    ];
});
