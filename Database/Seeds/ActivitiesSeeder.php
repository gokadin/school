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

        foreach (range(0, 50) as $index)
        {
            $this->dm->persist(new Activity(
                $teachers->at($this->faker->numberBetween(0, $teachers->count() - 1)),
                $this->faker->word,
                $this->faker->numberBetween(10, 80),
                $this->faker->word
            ));
        }

        $this->dm->flush();
    }
}