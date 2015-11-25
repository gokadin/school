<?php

namespace App\Repositories;

use App\Domain\School\School;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\TempTeacher;
use App\Domain\Users\Teacher;
use App\Domain\Users\Student;
use App\Domain\Common\Address;
use PDOException;

class UserRepository extends Repository
{
    protected $user;

    public function findTempTeacher($id)
    {
        return $this->dm->find(TempTeacher::class, $id);
    }

    public function preRegisterTeacher(array $data)
    {
        $this->dm->beginTransaction();

        try
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

            $this->dm->commit();
            return $tempTeacher;
        }
        catch (PDOException $e)
        {
            $this->dm->rollBack();
            $this->log->error('UserRepository.preRegisterTeacher : could not pre-register teacher : ' .$e->getMessage());
            return false;
        }
    }

    public function removeExpiredTempTeachers()
    {
        try
        {

        }
        catch (PDOException $e)
        {
            $this->log->error('UserRepository.removeExpiredTempTeachers : could not delete expired temp teachers : '
                .$e->getMessage());
        }
        $this->dm->queryBuilder()->table('temp_teachers')
            ->where('created_at', '<', 'DATE_SUB(NOW(), INTERVAL 1 DAY)')
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

        $this->dm->beginTransaction();

        try
        {
            $school = new School('Your School');
            $school->setAddress(new Address());

            $teacher = new Teacher($tempTeacher->firstName(), $tempTeacher->lastName(),
                $tempTeacher->email(), md5($data['password']), $subscription, new Address(), $school);
            $this->dm->persist($teacher);

            $this->dm->delete($tempTeacher);

            $this->dm->commit();
            return $teacher;
        }
        catch (PDOException $e)
        {
            $this->dm->rollBack();
            $this->log->error('UserRepository.registerTeacher : could not register teacher : '.$e->getMessage());
            return false;
        }
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