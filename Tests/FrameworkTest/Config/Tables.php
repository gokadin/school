<?php namespace Tests\FrameworkTest\Config;

use Library\Database\Table;

class Tables
{
    public function tests()
    {
        $t = new Table('Test');

        $t->increments('id');
        $t->string('col1', 32);
        $t->integer('col2');
        $t->timestamps();

        return $t;
    }

    public function teachers()
    {
        $t = new Table('Teacher');

        $t->increments('id');
        $t->integer('school_id')->nullable();
        $t->integer('address_id')->nullable();
        $t->string('name', 32);
        $t->timestamps();

        return $t;
    }

    public function students()
    {
        $t = new Table('Student');

        $t->increments('id');
        $t->integer('address_id')->nullable();
        $t->integer('school_id')->nullable();
        $t->integer('teacher_id');
        $t->string('name', 32);
        $t->timestamps();

        return $t;
    }

    public function schools()
    {
        $t = new Table('School');

        $t->increments('id');
        $t->integer('address_id')->nullable();
        $t->string('name');
        $t->timestamps();

        return $t;
    }

    public function posts()
    {
        $t = new Table('Post');

        $t->increments('id');
        $t->integer('student_id');
        $t->string('title');
        $t->string('content', 254)->nullable();
        $t->timestamps();

        return $t;
    }

    public function address()
    {
        $t = new Table('Address');

        $t->increments('id');
        $t->string('country')->nullable();
        $t->string('city')->nullable();
        $t->string('postal')->nullable();
        $t->string('civic')->nullable();
        $t->string('street')->nullable();
        $t->string('app')->nullable();
        $t->timestamps();

        return $t;
    }

    public function activities()
    {
        $t = new Table('Activity');

        $t->increments('id');
        $t->string('name');
        $t->decimal('rate', 6, 2)->nullable();
        $t->timestamps();

        return $t;
    }

    public function activity_student()
    {
        $t = new Table('ActivityStudent');

        $t->increments('id');
        $t->integer('activity_id');
        $t->integer('student_id');
        $t->timestamps();

        return $t;
    }
}