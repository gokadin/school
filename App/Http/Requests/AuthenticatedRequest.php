<?php

namespace App\Http\Requests;

use App\Domain\Services\LoginService;

abstract class AuthenticatedRequest extends Request
{
    /**
     * @var \App\Domain\Users\User
     */
    private $user;

    /**
     * @param LoginService $loginService
     */
    public function __construct(LoginService $loginService)
    {
        parent::__construct();

        $this->user = $loginService->user();
    }

    /**
     * @return \App\Domain\Users\User
     */
    protected function user()
    {
        return $this->user;
    }
}