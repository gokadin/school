<?php

namespace Database\Seeds;

use App\Domain\Activities\Activity;
use App\Domain\Users\Teacher;
use Library\DataMapper\Database\Seeder;

class ActivitiesSeeder extends Seeder
{
    public function run()
    {
        $teachers = $this->dm->findAll(Teacher::class);

        for ($i = 0; $i < 50; $i++)
        {
            $this->dm->persist(new Activity(
                $teachers->first(),
                $this->faker->word,
                $this->faker->numberBetween(10, 80),
                $this->faker->word
            ));
        }

        $this->dm->flush();
    }
}