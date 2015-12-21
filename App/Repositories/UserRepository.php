<?php

namespace App\Repositories;

use App\Domain\Activities\Activity;
use App\Domain\School\School;
use App\Domain\Setting\StudentRegistrationForm;
use App\Domain\Setting\TeacherSettings;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Transformers\StudentTransformer;
use App\Domain\Users\TempStudent;
use App\Domain\Users\TempTeacher;
use App\Domain\Users\Teacher;
use App\Domain\Users\Student;
use App\Domain\Common\Address;
use Library\DataMapper\DataMapper;
use Library\Log\Log;

class UserRepository extends Repository
{
    protected $user;
    protected $studentTransformer;

    public function __construct(DataMapper $dm, Log $log, StudentTransformer $studentTransformer)
    {
        parent::__construct($dm, $log);

        $this->studentTransformer = $studentTransformer;
    }

    public function findTempTeacher($id)
    {
        return $this->dm->find(TempTeacher::class, $id);
    }

    /**
     * @param $id
     * @return null|TempStudent
     */
    public function findTempStudent($id)
    {
        return $this->dm->find(TempStudent::class, $id);
    }

    public function findStudent($id)
    {
        return $this->dm->find(Student::class, $id);
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

    public function getNewStudentsOf(Teacher $teacher)
    {
        $ids = $this->dm->queryBuilder()->table('temp_students')
            ->where('teacher_id', '=', $teacher->getId())
            ->select(['id']);

        return $this->dm->findIn(TempStudent::class, $ids);
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
            ->where('created_at', '<', 'DATE_SUB(NOW(), INTERVAL '.TempStudent::DAYS_BEFORE_EXPIRING.' DAY)')
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

        $schoolAddress = new Address();
        $this->dm->persist($schoolAddress);
        $school = new School('Your School');
        $school->setAddress($schoolAddress);
        $this->dm->persist($school);

        $settings = new TeacherSettings(StudentRegistrationForm::defaultJson());
        $this->dm->persist($settings);

        $teacherAddress = new Address();
        $this->dm->persist($teacherAddress);
        $teacher = new Teacher($tempTeacher->firstName(), $tempTeacher->lastName(),
            $tempTeacher->email(), md5($data['password']), $subscription, $teacherAddress, $school, $settings);
        $this->dm->persist($teacher);

        $this->dm->delete($tempTeacher);

        $this->dm->flush();

        return $teacher;
    }

    public function registerStudent(Student $student, TempStudent $tempStudent)
    {
        $this->dm->persist($student->address());
        $this->dm->persist($student);

        $this->dm->delete($tempStudent);

        $this->dm->flush();
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

    public function getTeacherSettings()
    {
        $teacher = $this->user;

        $id = $this->dm->queryBuilder()->table('teacher_settings')
            ->where('teacher_id', '=', $teacher->getId())
            ->select(['id']);

        return $this->dm->find(TeacherSettings::class, $id);
    }

    public function updatePassword($password)
    {
        $this->getLoggedInUser()->setPassword($password);

        $this->dm->flush();
    }

    public function updatePersonalInfo(array $data)
    {
        $user = $this->getLoggedInUser();

        $user->setFirstName($data['firstName']);
        $user->setLastName($data['lastName']);
        $user->setEmail($data['email']);

        $this->dm->flush();
    }

    public function paginate($pageNumber, $pageCount, array $sortingRules = [], array $searchRules = [])
    {
        $students = $this->user->students();

        $result = $this->paginateCollection($students, $pageNumber, $pageCount, $sortingRules, $searchRules);

        return [
            'data' => $this->studentTransformer->transformCollection($result['data']),
            'pagination' => $result['pagination']
        ];
    }
}