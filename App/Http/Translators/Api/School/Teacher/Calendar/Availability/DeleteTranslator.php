<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Services\AvailabilityService;
use App\Domain\Users\Authenticator;
use App\Http\Translators\AuthenticatedTranslator;
use Library\Http\Request;
use Library\Transformer\Transformer;

class DeleteTranslator extends AuthenticatedTranslator
{
    /**
     * @var AvailabilityService
     */
    private $availabilityService;

    public function __construct(Authenticator $authenticator, Transformer $transformer, AvailabilityService $availabilityService)
    {
        parent::__construct($authenticator, $transformer);

        $this->availabilityService = $availabilityService;
    }

    public function translateRequest(Request $request)
    {
        $this->availabilityService->delete($this->user, $request->id);
    }
}