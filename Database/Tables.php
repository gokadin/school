<?php namespace Database;

use Library\Database\Table;

class Tables
{
    protected function users()
    {
        $t = new Table('user');

        $t->increments('id');
        $t->integer('school_id');
        $t->string('first_name', 32);
        $t->string('last_name', 32);
        $t->string('email')->unique();
        $t->string('password');
        $t->string('phone', 32)->nullable();
        $t->integer('type');
        $t->boolean('active')->default(1);
        $t->timestamps();

        return $t;
    }

    protected function schools()
    {
        $t = new Table('school');

        $t->increments('id');
        $t->string('name');
        $t->timestamps();

        return $t;
    }

    protected function activities()
    {
        $t = new Table('activity');

        $t->increments('id');
        $t->integer('teacher_id');
        $t->string('name');
        $t->decimal('rate');
        $t->integer('period');
        $t->boolean('active')->default('1');
        $t->timestamps();

        return $t;
    }
}
