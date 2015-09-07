<?php

namespace App\Domain\Subscriptions;

final class SubscriptionsTypes
{
    const TRIAL_DURATION_DAYS = 30;
    const SUBSCRIPTION_COUNT = 4;
    const SUB_1_NAME = 'Basic';
    const SUB_1_DEFAULT_RATE = 0.0;
    const SUB_1_NUM_STUDENTS = 5;
    const SUB_1_STORAGE = 1;
    const SUB_2_NAME = 'Silver';
    const SUB_2_DEFAULT_RATE = 14.99;
    const SUB_2_NUM_STUDENTS = 20;
    const SUB_2_STORAGE = 5;
    const SUB_3_NAME = 'Gold';
    const SUB_3_DEFAULT_RATE = 24.99;
    const SUB_3_NUM_STUDENTS = 50;
    const SUB_3_STORAGE = 7;
    const SUB_4_NAME = 'Platinum';
    const SUB_4_DEFAULT_RATE = 39.99;
    const SUB_4_NUM_STUDENTS = -1;
    const SUB_4_STORAGE = 10;

    public static function describeSubscriptions()
    {
        $subscriptions = array();

        $subscriptions[] = [
            'name' => self::SUB_1_NAME,
            'price' => 'FREE',
            'numStudents' => 5,
            'storageSpace' => '1GB'
        ];

        $subscriptions[] = [
            'name' => 'Silver',
            'price' => '14.99 / month',
            'numStudents' => 20,
            'storageSpace' => '5GB'
        ];

        $subscriptions[] = [
            'name' => 'Gold',
            'price' => '25.99 / month',
            'numStudents' => 50,
            'storageSpace' => '7GB'
        ];

        $subscriptions[] = [
            'name' => 'Platinum',
            'price' => '39.99 / month',
            'numStudents' => 'unlimited',
            'storageSpace' => '10GB'
        ];

        return $subscriptions;
    }
}