<?php

namespace App\Domain\Services;

use App\Domain\Users\Teacher;

class AccountService extends AuthenticatedService
{
    public function updatePersonalInfo(array $data)
    {
        $this->user->setFirstName($data['firstName']);
        $this->user->setLastName($data['lastName']);
        $this->user->setEmail($data['email']);

        $this->repository->of(Teacher::class)->update($this->user);
    }

    public function updatePassword(array $data)
    {
        if ($this->user->password() != md5($data['currentPassword']))
        {
            return false;
        }

        $this->user->setPassword(md5($data['newPassword']));

        $this->repository->of(Teacher::class)->update($this->user);

        return true;
    }
}