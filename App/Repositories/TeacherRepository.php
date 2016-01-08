<?php

namespace App\Repositories;

use App\Domain\Common\Address;
use App\Domain\School\School;
use App\Domain\Setting\StudentRegistrationForm;
use App\Domain\Setting\TeacherSettings;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\Teacher;
use App\Domain\Users\TempTeacher;

class TeacherRepository extends RepositoryBase
{
    public function preRegister(array $data)
    {
        $subscription = new Subscription($data['subscriptionType']);
        $this->dm->persist($subscription);

        $confirmationCode = md5(rand(999, 999999));

        $tempTeacher = new TempTeacher($data['firstName'], $data['lastName'], $data['email'],
            $subscription, $confirmationCode);
        $this->dm->persist($tempTeacher);

        $this->dm->flush();

        return $tempTeacher;
    }

    public function findTempTeacher($id)
    {
        return $this->dm->find(TempTeacher::class, $id);
    }

    public function create(array $data)
    {
        $tempTeacher = $this->findTempTeacher($data['tempTeacherId']);
        if (is_null($tempTeacher))
        {
            return null;
        }

        $subscription = $this->dm->find(Subscription::class, $tempTeacher->subscription()->getId());
        if (is_null($subscription))
        {
            return null;
        }

        $schoolAddress = new Address();
        $this->dm->persist($schoolAddress);

        $school = new School('Your School', $schoolAddress);
        $school->setAddress($schoolAddress);
        $this->dm->persist($school);

        $settings = new TeacherSettings(StudentRegistrationForm::defaultJson());
        $this->dm->persist($settings);

        $teacherAddress = new Address();
        $this->dm->persist($teacherAddress);

        $teacher = new Teacher($tempTeacher->firstName(), $tempTeacher->lastName(),
            $tempTeacher->email(), md5($data['password']), $subscription, $teacherAddress, $school, $settings);
        $this->dm->persist($teacher);

        $this->dm->delete($tempTeacher);

        $this->dm->flush();

        return $teacher;
    }

    public function removeExpiredTempTeachers()
    {
        $this->dm->queryBuilder()->table('temp_teachers')
            ->where('created_at', '<', 'DATE_SUB(NOW(), INTERVAL 1 DAY)')
            ->delete();
    }
}