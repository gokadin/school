<?php

namespace App\Repositories\Contracts;

interface IUserRepository extends IRepository
{
    function findTempTeacher($id);

    function preRegisterTeacher(array $data);

    function registerTeacher(array $data);
}