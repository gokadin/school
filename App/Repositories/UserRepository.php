<?php

namespace App\Repositories;

use Carbon\Carbon;
use Library\Facades\DB;
use Library\Facades\Redis;
use Library\Redis\RedisModel;
use Models\TempTeacher;
use Models\Subscription;
use Models\Address;
use Models\Teacher;
use Models\TeacherSetting;
use Models\School;
use PDOException;

class UserRepository
{
    public function findTempTeacher($id)
    {
        $r = Redis::getRedis();
        return new RedisModel($r->hgetall('tempTeacher:'.$id));
    }

    public function preRegisterTeacher(array $data)
    {
        $r = Redis::getRedis();

        $subscriptionId = $r->incr('next_subscription_id');
        $r->hmset('subscription:'.$subscriptionId, [
            'type' => $data['subscriptionType'],
            'created_at' => Carbon::now()
        ]);

        $confirmationCode = md5(rand(999, 999999));
        $r->hmset('tempTeacher:'.$r->incr('next_temp_teacher_id'), [
            'subscription_id' => $subscriptionId,
            'first_name' => $data['firstName'],
            'last_name' => $data['lastName'],
            'email' => $data['email'],
            'confirmation_code' => $confirmationCode
        ]);

        return $r->get('next_temp_teacher_id');

//            $subscription = Subscription::create([
//                'type' => $data['subscriptionType']
//            ]);
//
//            $confirmationCode = md5(rand(999, 999999));
//
//            $tempTeacher = TempTeacher::create([
//                'subscription_id' => $subscription->id,
//                'first_name' => $data['firstName'],
//                'last_name' => $data['lastName'],
//                'email' => $data['email'],
//                'confirmation_code' => $confirmationCode
//            ]);
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