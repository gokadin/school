<?php

namespace App\Http\Translators\Api\Frontend\Account;

use App\Domain\Services\LoginService;
use App\Domain\Users\Authenticator;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class LoginTranslator extends AuthenticatedTranslator
{
    /**
     * @var LoginService
     */
    private $loginService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, LoginService $loginService)
    {
        parent::__construct($authenticator, $transformer);

        $this->loginService = $loginService;
    }

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->loginService->login($request->email, $request->password));
    }

    private function translateResponse(string $authToken): array
    {
        return [
            'authToken' => $authToken
        ];
    }
}