<?php

namespace Database\Seeds;

use App\Domain\Common\Address;
use App\Domain\School\School;
use App\Domain\Setting\StudentRegistrationForm;
use App\Domain\Setting\TeacherSettings;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\Teacher;
use Library\DataMapper\Database\Seeder;

class TeachersSeeder extends Seeder
{
    public function run()
    {
        $sub = new Subscription(1);
        $this->dm->persist($sub);

        $schoolAddress = new Address();
        $this->dm->persist($schoolAddress);

        $school = new School($this->faker->word, $schoolAddress);
        $this->dm->persist($school);

        $teacherSettings = new TeacherSettings(StudentRegistrationForm::defaultJson());
        $this->dm->persist($teacherSettings);

        $teacherAddress = new Address();
        $this->dm->persist($teacherAddress);

        $this->dm->persist(new Teacher(
            $this->faker->firstName,
            $this->faker->lastName,
            'admin@admin.com',
            md5('admin'),
            $sub,
            $teacherAddress,
            $school,
            $teacherSettings
        ));

        $this->dm->flush();
    }
}