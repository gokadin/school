<?php

namespace Database\Seeds;

use Library\DataMapper\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call(TeachersSeeder::class);
        $this->call(ActivitiesSeeder::class);
    }
}