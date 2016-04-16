<?php

namespace App\Http\Translators\Api\Frontend\Account;

use App\Domain\Users\User;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;

class CurrentUserTranslator extends AuthenticatedTranslator
{
    public function translateRequest(Request $request)
    {
        return $this->translateResponse($this->user);
    }

    private function translateResponse($user): array
    {
        return is_null($user)
            ? [
                'loggedIn' => false
            ]
            : [
                'loggedIn' => true,
                'currentUser' => $this->transformer->of(User::class)->transform($user)
            ];
    }
}