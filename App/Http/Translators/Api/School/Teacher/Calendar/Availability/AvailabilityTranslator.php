<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Services\AvailabilityService;
use App\Domain\Users\Authenticator;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Transformer\Transformer;

abstract class AvailabilityTranslator extends AuthenticatedTranslator
{
    /**
     * @var AvailabilityService
     */
    protected $availabilityService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, AvailabilityService $availabilityService)
    {
        parent::__construct($authenticator, $transformer);

        $this->availabilityService = $availabilityService;
    }
}