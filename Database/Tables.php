<?php namespace Database;

use Library\Database\Blueprint;

class Tables
{
    protected function users()
    {
        $table = new Blueprint('user');

        $table->increments('id');
        $table->integer('school_id');
        $table->string('first_name', 32);
        $table->string('last_name', 32);
        $table->string('email')->unique();
        $table->string('password');
        $table->string('phone', 32)->nullable();
        $table->integer('type');
        $table->boolean('active');
        $table->timestamps();

        return $table;
    }

    protected function schools()
    {
        $table = new Blueprint('school');

        $table->increments('id');
        $table->string('name');
        $table->timestamps();

        return $table;
    }
}
