<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Calendar\Availability;
use Carbon\Carbon;
use Library\Http\Request;

class UpdateTranslator extends AvailabilityTranslator
{
    public function translateRequest(Request $request)
    {
        $availability = new Availability(Carbon::parse($request->date), $request->startTime, $request->endTime);
        $availability->setUniqueId($request->id);

        $this->availabilityService->update($this->user, $availability);
    }
}