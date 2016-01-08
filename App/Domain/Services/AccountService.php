<?php

namespace App\Domain\Services;

use App\Domain\Users\Teacher;

class AccountService extends AuthenticatedService
{
    public function updatePersonalInfo(array $data)
    {
        $teacher = $this->user;

        $teacher->setFirstName($data['firstName']);
        $teacher->setLastName($data['lastName']);
        $teacher->setEmail($data['email']);

        $this->repository->of(Teacher::class)->update($teacher);
    }

    public function updatePassword(array $data)
    {
        if ($this->user->password() != md5($data['currentPassword']))
        {
            return false;
        }

        $this->userRepository->updatePassword(md5($data['newPassword']));

        return true;
    }
}