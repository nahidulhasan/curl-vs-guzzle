<?php

$factory->define(App\Models\WelcomeInfo::class, function (Faker\Generator $faker) {
    return [
        'guest_salutation' => $faker->title,
        'user_salutation' => $faker->title,
        'guest_message' => $faker->text,
        'user_message' => $faker->text
    ];
});

