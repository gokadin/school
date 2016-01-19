<?php

namespace App\Domain\Services;

use App\Domain\Users\User;

class UserService extends AuthenticatedService
{
    public function currentUser()
    {
        return $this->transformer->of(User::class)->transform($this->user);
    }
}