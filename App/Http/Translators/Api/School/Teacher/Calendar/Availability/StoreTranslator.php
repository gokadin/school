<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Calendar\Availability;
use Carbon\Carbon;
use Library\Http\Request;

class StoreTranslator extends AvailabilityTranslator
{
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