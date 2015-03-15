<?php namespace Database;

use Library\Database\Table;

class Tables
{
    protected function users()
    {
        $t = new Table('User');

        $t->increments('id');
        $t->integer('address_id');
        $t->integer('user_setting_id');
        $t->string('first_name', 32);
        $t->string('last_name', 32);
        $t->string('email')->unique();
        $t->string('password');
        $t->string('phone', 32)->nullable();
        $t->boolean('active')->default(1);
        $t->meta();
        $t->timestamps();

        return $t;
    }

    protected function user_settings()
    {
        $t = new Table('UserSetting');

        $t->increments('id');
        $t->boolean('show_email')->default(1);
        $t->boolean('show_address')->default(1);
        $t->boolean('show_phone')->default(1);
        $t->timestamps();

        return $t;
    }

    protected function teachers()
    {
        $t = new Table('Teacher');

        $t->increments('id');
        $t->integer('school_id');
        $t->integer('plan', 5)->default(1);
        $t->integer('type', 5)->default(1);

        return $t;
    }

    protected function students()
    {
        $t = new Table('Student');

        $t->increments('id');
        $t->integer('school_id');
        $t->integer('teacher_id');
        $t->integer('type', 5)->default(1);

        return $t;
    }

    protected function schools()
    {
        $t = new Table('School');

        $t->increments('id');
        $t->integer('address_id');
        $t->string('name');
        $t->timestamps();

        return $t;
    }

    protected function activities()
    {
        $t = new Table('Activity');

        $t->increments('id');
        $t->integer('teacher_id');
        $t->string('name');
        $t->decimal('rate');
        $t->integer('period');
        $t->string('location')->nullable();
        $t->boolean('active')->default('1');
        $t->timestamps();

        return $t;
    }

    protected function activity_student()
    {
        $t = new Table('ActivityStudent');

        $t->increments('id');
        $t->integer('activity_id');
        $t->integer('student_id');
        $t->timestamps();

        return $t;
    }

    protected function addresses()
    {
        $t = new Table('Address');

        $t->increments('id');
        $t->string('country', 20)->nullable();
        $t->string('state', 20)->nullable();
        $t->string('city', 20)->nullable();
        $t->string('postal_code', 10)->nullable();
        $t->string('street')->nullable();
        $t->string('civic_number', 10)->nullable();
        $t->string('app_number', 10)->nullable();
        $t->timestamps();

        return $t;
    }
}
