<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Calendar\Availability;
use Carbon\Carbon;
use Library\Http\Request;

class DestroyTranslator extends AvailabilityTranslator
{
    public function translateRequest(Request $request)
    {
        $availability = new Availability(Carbon::parse($request->weekStartDate), 0, 0);
        $availability->setUniqueId($request->id);

        $this->availabilityService->destroy($this->user, $availability);
    }
}