<?php namespace Tests\FrameworkTest\Database;

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
        $t->string('name', 32);
        $t->timestamps();

        return $t;
    }

    public function students()
    {
        $t = new Table('Student');

        $t->increments('id');
        $t->integer('teacher_id');
        $t->string('name', 32);
        $t->timestamps();

        return $t;
    }
}