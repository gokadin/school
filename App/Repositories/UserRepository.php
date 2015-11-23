<?php

namespace App\Repositories;

use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\TempTeacher;
use App\Domain\Users\Teacher;
use PDOException;

class UserRepository extends Repository
{
    public function findTempTeacher($id)
    {
        // ...
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
        $tempTeacher = TempTeacher::find($data['tempTeacherId']);
        if ($tempTeacher == null)
        {
            return false;
        }

        $subscription = Subscription::find($tempTeacher->subscription_id);
        if ($subscription == null)
        {
            return false;
        }

        DB::beginTransaction();

        try
        {
            $school = School::create([
                'name' => 'Your School',
                'address_id' => Address::create()->id
            ]);

            $teacher = Teacher::create([
                'subscription_id' => $subscription->id,
                'address_id' => Address::create()->id,
                'teacher_setting_id' => TeacherSetting::create()->id,
                'school_id' => $school->id,
                'first_name' => $tempTeacher->first_name,
                'last_name' => $tempTeacher->last_name,
                'email' => $tempTeacher->email,
                'password' => md5($data['password']),
            ]);

            DB::commit();
            return $teacher;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
    }
}