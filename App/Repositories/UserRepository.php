<?php

namespace App\Repositories;

use App\Domain\School\School;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\TempTeacher;
use App\Domain\Users\Teacher;
use App\Domain\Common\Address;
use PDOException;

class UserRepository extends Repository
{
    public function findTempTeacher($id)
    {
        return $this->dm->find(TempTeacher::class, $id);
    }

    public function preRegisterTeacher(array $data)
    {
        $this->dm->beginTransaction();

        try
        {
            $subscription = new Subscription($data['subscriptionType']);
            $this->dm->persist($subscription);

            $confirmationCode = md5(rand(999, 999999));

            $tempTeacher = new TempTeacher(
                $data['firstName'],
                $data['lastName'],
                $data['email'],
                $subscription,
                $confirmationCode
            );
            $this->dm->persist($tempTeacher);

            $this->dm->commit();
            return $tempTeacher;
        }
        catch (PDOException $e)
        {
            $this->dm->rollBack();
            return false;
        }
    }

    public function removeExpiredTempTeachers()
    {
        $this->dm->queryBuilder()->table('temp_teachers')
            ->where('created_at', '<', 'DATE_SUB(NOW(), INTERVAL 1 DAY)')
            ->delete();
    }

    public function registerTeacher(array $data)
    {
        $tempTeacher = $this->findTempTeacher($data['tempTeacherId']);
        if (is_null($tempTeacher))
        {
            return false;
        }

        $subscription = $this->dm->find(Subscription::class, $tempTeacher->subscription()->getId());
        if (is_null($subscription))
        {
            return false;
        }

        $this->dm->beginTransaction();

        try
        {
            $school = new School('Your School');
            $school->setAddress(new Address());

            $teacher = new Teacher($tempTeacher->firstName(), $tempTeacher->lastName(),
                $tempTeacher->email(), md5($data['password']), $subscription, new Address(), $school);
            $this->dm->persist($teacher);

            $this->dm->delete($tempTeacher);

            $this->dm->commit();
            return $teacher;
        }
        catch (PDOException $e)
        {
            $this->dm->rollBack();
            return false;
        }
    }
}