<?php

use Faker\Generator as Faker;

$factory->define(Scaupize1123\JustOfficalNews\NewsCategoryTranslation::class, function (Faker $faker) {
    return [
       'name' => $faker->text($maxNbChars = 99),
       'language_id' => 1,
       'news_category_id' => 1,
       'status' => 1,
       'created_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
       'updated_at' => $faker->date($format = 'Y-m-d', $max = 'now'),
    ];
});
