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
}