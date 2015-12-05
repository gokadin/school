<?php

namespace App\Repositories;

use App\Domain\Activities\Activity;
use App\Domain\School\School;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\TempStudent;
use App\Domain\Users\TempTeacher;
use App\Domain\Users\Teacher;
use App\Domain\Users\Student;
use App\Domain\Common\Address;

class UserRepository extends Repository
{
    protected $user;

    public function findTempTeacher($id)
    {
        return $this->dm->find(TempTeacher::class, $id);
    }

    public function preRegisterTeacher(array $data)
    {
        $subscription = new Subscription($data['subscriptionType']);
        $this->dm->persist($subscription);

        $confirmationCode = md5(rand(999, 999999));

        $tempTeacher = new TempTeacher(
            $data['firstName'],
            $data['lastName'],
            $data['email'],
            $subscription,
            $confirmationCode
        );
        $this->dm->persist($tempTeacher);

        $this->dm->flush();

        return $tempTeacher;
    }

    public function preRegisterStudent(Teacher $teacher, Activity $activity, array $data)
    {
        $confirmationCode = md5(rand(999, 999999));

        $tempStudent = new TempStudent($teacher, $activity, $data['firstName'], $data['lastName'],
            $data['email'], $confirmationCode);
        $this->dm->persist($tempStudent);

        $this->dm->flush();

        return $tempStudent;
    }

    public function removeExpiredTempTeachers()
    {
        $this->dm->queryBuilder()->table('temp_teachers')
            ->where('created_at', '<', 'DATE_SUB(NOW(), INTERVAL 1 DAY)')
            ->delete();
    }

    public function removeExpiredTempStudents()
    {
        $this->dm->queryBuilder()->table('temp_students')
            ->where('created_at', '<', 'DATE_SUB(NOW(), INTERVAL 7 DAY)')
            ->delete();
    }

    public function registerTeacher(array $data)
    {
        $tempTeacher = $this->findTempTeacher($data['tempTeacherId']);
        if (is_null($tempTeacher))
        {
            return false;
        }

        $subscription = $this->dm->find(Subscription::class, $tempTeacher->subscription()->getId());
        if (is_null($subscription))
        {
            return false;
        }

        $school = new School('Your School');
        $school->setAddress(new Address());

        $teacher = new Teacher($tempTeacher->firstName(), $tempTeacher->lastName(),
            $tempTeacher->email(), md5($data['password']), $subscription, new Address(), $school);
        $this->dm->persist($teacher);

        $this->dm->delete($tempTeacher);

        $this->dm->flush();

        return $teacher;
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

    public function getLoggedInUser()
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
        }
    }

    public function getLoggedInType()
    {
        return $_SESSION['type'];
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
}