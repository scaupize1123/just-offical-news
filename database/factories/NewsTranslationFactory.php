<?php

use Faker\Generator as Faker;

$factory->define(Scaupize1123\JustOfficalNews\NewsTranslation::class, function (Faker $faker) {
    return [
       'name' => $faker->text($maxNbChars = 200),
       'brief' => $faker->text($maxNbChars = 200),
       'desc' => $faker->paragraph(10, true),
       'language_id' => 1,
       'news_id' => 1,
       'status' => 1,
    ];
});
