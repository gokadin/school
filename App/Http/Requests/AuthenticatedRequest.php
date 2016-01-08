<?php

namespace App\Http\Requests;

use App\Domain\Users\Authenticator;

abstract class AuthenticatedRequest extends Request
{
    protected $user;

    public function __construct(Authenticator $authenticator)
    {
        parent::__construct();

        $this->user = $authenticator->user();
    }
}