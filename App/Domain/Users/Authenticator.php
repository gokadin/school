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

    /**
     * @var string
     */
    private $type;

    public function __construct(DataMapper $dm)
    {
        $this->dm = $dm;
    }

    public function user()
    {
        return $this->user;
    }

    public function type()
    {
        return $this->type;
    }

    public function processAuthorization($jwt)
    {
        $jwt = explode('Bearer ', $jwt)[1];
        $jwt = (array) JWT::decode($jwt, $this->getJwtSecret(), array('HS512'));
        $data = (array) $jwt['data'];

        $user = null;
        switch ($data['type'])
        {
            case 'teacher':
                $user = $this->dm->find(Teacher::class, $data['id']);
                break;
            case 'student':
                $user = $this->dm->find(Student::class, $data['id']);
                break;
            default:
                return false;
        }

        if (is_null($user))
        {
            return false;
        }

        $this->user = $user;
        $this->type = $data['type'];
        return true;
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

    private function getJwtSecret()
    {
        return base64_encode('*ei3%a9200-h');
    }
}