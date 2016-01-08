<?php

namespace App\Domain\Users;

use Library\DataMapper\DataMapper;

class Authenticator
{
    /**
     * @var DataMapper
     */
    private $dm;

    /**
     * @var User
     */
    private $user;

    public function __construct(DataMapper $dm)
    {
        $this->dm = $dm;
    }

    public function logout()
    {
        session_destroy();
    }

    public function loggedIn()
    {
        return isset($_SESSION['id']) &&
            isset($_SESSION['type']) &&
            isset($_SESSION['authenticated']) &&
            $_SESSION['authenticated'];
    }

    public function loginTeacher(Teacher $teacher)
    {
        $_SESSION['id'] = $teacher->getId();
        $_SESSION['type'] = 'teacher';
        $_SESSION['authenticated'] = true;

        $this->user = $teacher;
    }

    public function loginStudent(Student $student)
    {
        $_SESSION['id'] = $student->getId();
        $_SESSION['type'] = 'student';
        $_SESSION['authenticated'] = true;

        $this->user = $student;
    }

    public function attemptLogin($class, $email, $password)
    {
        $user = $this->dm->findOneBy($class, [
            'email' => $email,
            'password' => $password
        ]);

        if (is_null($user))
        {
            return false;
        }

        if ($user instanceof Teacher)
        {
            $this->loginTeacher($user);
        }
        else if ($user instanceof Student)
        {
            $this->loginStudent($user);
        }

        return $user;
    }

    public function user()
    {
        if (!is_null($this->user))
        {
            return $this->user;
        }

        switch ($_SESSION['type'])
        {
            case 'teacher':
                return $this->user = $this->dm->find(Teacher::class, $_SESSION['id']);
            case 'student':
                return $this->user = $this->dm->find(Student::class, $_SESSION['id']);
            default:
                return null;
        }
    }

    public function type()
    {
        return $_SESSION['type'];
    }
}