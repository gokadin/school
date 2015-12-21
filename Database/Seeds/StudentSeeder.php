<?php

namespace Database\Seeds;

use App\Domain\Activities\Activity;
use App\Domain\Common\Address;
use App\Domain\Users\Student;
use App\Domain\Users\Teacher;
use Library\DataMapper\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $activities = $this->dm->findAll(Activity::class);
        $teachers = $this->dm->findAll(Teacher::class);

        for ($i = 0; $i < 50; $i++)
        {
            $address = new Address();
            $this->dm->persist($address);

            $this->dm->persist(new Student(
                $this->faker->firstName,
                $this->faker->lastName,
                $this->faker->email,
                md5('admin'),
                $address,
                $activities->at($this->faker->numberBetween(0, $activities->count() - 1)),
                $teachers->first()
            ));
        }

        $this->dm->flush();
    }
}