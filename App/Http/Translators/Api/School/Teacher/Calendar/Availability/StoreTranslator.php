<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Calendar\Availability;
use App\Domain\Services\AvailabilityService;
use App\Domain\Users\Authenticator;
use App\Http\Translators\AuthenticatedTranslator;
use Carbon\Carbon;
use Library\Http\Request;
use Library\Transformer\Transformer;

class StoreTranslator extends AuthenticatedTranslator
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

    public function translateRequest(Request $request): array
    {
        return $this->translateResponse($this->availabilityService->store(new Availability(
            $this->user, Carbon::parse($request->date), $request->startTime, $request->endTime
        )));
    }

    private function translateResponse(int $id): array
    {
        return [
            'id' => $id
        ];
    }
}