<?php

namespace App\Http\Translators\Api\Frontend\Account;

use App\Domain\Users\Authenticator;
use App\Domain\Users\User;
use App\Http\Translators\Translator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class CurrentUserTranslator extends Translator
{
    /**
     * @var Authenticator
     */
    private $authenticator;

    public function __construct(Transformer $transformer, Authenticator $authenticator)
    {
        parent::__construct($transformer);

        $this->authenticator = $authenticator;
    }

    public function translateRequest(Request $request)
    {
        $this->authenticator->processAuthorization($request->header('Authorization'));

        return $this->translateResponse($this->authenticator->user());
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