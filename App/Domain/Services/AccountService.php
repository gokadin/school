<?php

namespace App\Domain\Services;

class AccountService extends AuthenticatedService
{
    public function updatePersonalInfo(array $data)
    {
        $this->userRepository->updatePersonalInfo($data);

        return true;
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