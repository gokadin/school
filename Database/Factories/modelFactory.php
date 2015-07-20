<?php

$factory->define(\Models\Teacher::class, function($faker) {
    return [
        'subscription_id' => 1,
        'address_id' => 1,
        'teacher_setting_id' => 1,
        'school_id' => 1,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => md5($faker->word)
    ];
});