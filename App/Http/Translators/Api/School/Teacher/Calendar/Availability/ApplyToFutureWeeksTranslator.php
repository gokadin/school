<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use Carbon\Carbon;
use Library\Http\Request;

class ApplyToFutureWeeksTranslator extends AvailabilityTranslator
{
    public function translateRequest(Request $request)
    {
        $this->availabilityService->applyToFutureWeeks($this->user, Carbon::parse($request->date));
    }
}