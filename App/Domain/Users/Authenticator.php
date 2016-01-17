<?php

namespace App\Domain\Users;

use Firebase\JWT\JWT;
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
            return ['authToken' => $this->loginTeacher($user), 'user' => $user];
        }
        else if ($user instanceof Student)
        {
            if (!$user->hasAccount())
            {
                return false;
            }

            return ['authToken' => $this->loginStudent($user), 'user' => $user];
        }

        return false;
    }

    private function loginTeacher($user)
    {
        return $this->createJwt([
            'id' => $user->getId(),
            'type' => 'teacher'
        ]);
    }

    private function loginStudent($user)
    {
        return $this->createJwt([
            'id' => $user->getId(),
            'type' => 'student'
        ]);
    }

    private function createJwt(array $data)
    {
        $currentTime = time();

        $data = [
            'iat' => $currentTime,
            'iss' => 'instructioner',
            'nbf' => $currentTime,
            'data' => $data
        ];

        return JWT::encode($data, $this->getJwtSecret(), 'HS512');
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

    private function getJwtSecret()
    {
        return base64_encode('*ei3%a9200-h');
    }
}