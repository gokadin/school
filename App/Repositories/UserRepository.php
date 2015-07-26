<?php

namespace App\Repositories;

use App\Repositories\Contracts\IUserRepository;
use Models\TempTeacher;
use PDOException;

class UserRepository implements IUserRepository
{
    public function findTempTeacher($id)
    {
        return TempTeacher::find($id);
    }

    public function preRegisterTeacher(array $data)
    {
        DB::beginTransaction();

        try
        {
            $subscription = Subscription::create([
                'type' => $data['subscriptionType']
            ]);

            $confirmationCode = md5(rand(999, 999999));

            TempTeacher::create([
                'subscription_id' => $subscription->id,
                'first_name' => $data['firstName'],
                'last_name' => $data['lastName'],
                'email' => $data['email'],
                'confirmation_code' => $confirmationCode
            ]);

            DB::commit();
            return true;
        }
        catch (PDOException $e)
        {
            DB::rollBack();
            return false;
        }
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