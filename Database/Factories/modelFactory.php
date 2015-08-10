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

$factory->define(\Models\Student::class, function($faker) {
    return [
        'teacher_id' => 1,
        'address_id' => 1,
        'student_setting_id' => 1,
        'school_id' => 1,
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->email,
        'password' => md5($faker->word)
    ];
});

$factory->define(\Models\Activity::class, function($faker) {
    return [
        'teacher_id' => 1,
        'name' => $faker->word,
        'rate' => 60,
        'period' => 1
    ];
});

$factory->define(\Models\School::class, function($faker) {
    return [
        'address_id' => 1,
        'name' => $faker->word
    ];
});

$factory->define(\Models\ActivityStudent::class, function($faker) {
    return [
        'student_id' => 1,
        'activity_id' => 1,
        'rate' => 60,
        'start_day' => 15
    ];
});