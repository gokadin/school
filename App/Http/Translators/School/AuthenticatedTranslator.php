<?php

namespace App\Http\Translators\School;

use App\Domain\Users\Authenticator;
use App\Domain\Users\User;
use App\Http\Translators\Translator;
use Library\Transformer\Transformer;

abstract class AuthenticatedTranslator extends Translator
{
    /**
     * @var User
     */
    protected $user;

    public function __construct(Authenticator $authenticator, Transformer $transformer)
    {
        parent::__construct($transformer);

        $this->user = $authenticator->user();
    }
}