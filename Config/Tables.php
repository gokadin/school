<?php namespace Config;

use Library\Database\Table;

class Tables
{
    protected function teacher_settings()
    {
        $t = new Table('TeacherSetting');

        $t->increments('id');
        $t->boolean('show_email')->default(1);
        $t->boolean('show_address')->default(1);
        $t->boolean('show_phone')->default(1);
        $t->timestamps();

        return $t;
    }

    protected function student_settings()
    {
        $t = new Table('StudentSetting');

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
        $t->integer('subscription_id');
        $t->integer('address_id');
        $t->integer('teacher_setting_id');
        $t->integer('school_id');
        $t->string('first_name', 32);
        $t->string('last_name', 32);
        $t->string('email')->unique();
        $t->string('password');
        $t->string('phone', 32)->nullable();
        $t->integer('type', 5)->default(1);
        $t->boolean('active')->default(1);
        $t->string('profile_picture', 200)->default('"'.\Library\Config::get('defaultProfilePicturePath').'"');
        $t->timestamps();

        return $t;
    }

    protected function students()
    {
        $t = new Table('Student');

        $t->increments('id');
        $t->integer('teacher_id');
        $t->integer('address_id');
        $t->integer('student_setting_id');
        $t->integer('school_id');
        $t->string('first_name', 32);
        $t->string('last_name', 32);
        $t->string('email')->unique();
        $t->string('password');
        $t->string('phone', 32)->nullable();
        $t->integer('type', 5)->default(1);
        $t->boolean('active')->default(1);
        $t->string('profile_picture', 200)->default('"'.\Library\Config::get('defaultProfilePicturePath').'"');
        $t->timestamps();

        return $t;
    }

    public function temp_teachers()
    {
        $t = new Table('TempTeacher');

        $t->increments('id');
        $t->integer('subscription_id');
        $t->integer('type', 5)->default(1);
        $t->string('first_name');
        $t->string('last_name');
        $t->string('email');
        $t->string('confirmation_code');
        $t->timestamps();

        return $t;
    }

    protected function subscriptions()
    {
        $t = new Table('Subscription');

        $t->increments('id');
        $t->integer('type', 5)->default(1);
        $t->decimal('custom_rate', 6, 2)->default(-1.0);
        $t->integer('period', 5)->default(1);
        $t->timestamps();

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
        $t->decimal('rate', 6, 2);
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

    protected function teacher_events()
    {
        $t = new Table('TeacherEvent');

        $t->increments('id');
        $t->integer('teacher_id');
        $t->string('title');
        $t->datetime('start_date');
        $t->datetime('end_date');
        $t->boolean('is_all_day');
        $t->string('start_time', 12);
        $t->string('end_time', 12);
        $t->boolean('is_recurring');
        $t->string('recurring_repeat', 12);
        $t->integer('recurring_every');
        $t->boolean('is_recurring_ends_never');
        $t->datetime('recurring_end_date');
        $t->string('description', 255);
        $t->string('color', 16);
        $t->string('location');
        $t->string('visibility', 12);
        $t->string('student_ids');
        $t->integer('activity_id');
        $t->string('notify_me_by', 12);
        $t->integer('notify_me_before');
        $t->timestamps();

        return $t;
    }

    protected function teacher_messages()
    {
        $t = new Table('TeacherMessage');

        $t->increments('id');
        $t->integer('teacher_id');
        $t->integer('recipient_id');
        $t->string('recipient_type', 32);
        $t->string('subject');
        $t->text('content');
        $t->boolean('is_read')->default(0);
        $t->timestamps();

        return $t;
    }

    protected function student_messages()
    {
        $t = new Table('StudentMessage');

        $t->increments('id');
        $t->integer('student_id');
        $t->integer('recipient_id');
        $t->string('recipient_type', 32);
        $t->string('subject');
        $t->text('content');
        $t->boolean('is_read')->default(0);
        $t->timestamps();

        return $t;
    }
}
