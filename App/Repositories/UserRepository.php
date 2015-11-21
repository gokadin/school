<?php

namespace App\Repositories;

use Library\Facades\DB;
use Models\TempTeacher;
use App\Domain\Subscriptions\Subscription;
use Models\Address;
use Models\Teacher;
use Models\TeacherSetting;
use Models\School;
use PDOException;

class UserRepository extends Repository
{
    public function findTempTeacher($id)
    {
        // ...
    }

    public function preRegisterTeacher(array $data)
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

        return $tempTeacher;
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