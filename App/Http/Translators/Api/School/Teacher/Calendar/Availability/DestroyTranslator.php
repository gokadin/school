<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Calendar\Availability;
use Carbon\Carbon;
use Library\Http\Request;

class DestroyTranslator extends AvailabilityTranslator
{
    public function translateRequest(Request $request)
    {
        $parts = explode('a', $request->id);
        if (sizeof($parts) != 2)
        {
            return false;
        }

        $availability = new Availability(Carbon::parse($parts[1]), 0, 0);
        $availability->setUniqueId($parts[0]);

        $this->availabilityService->destroy($this->user, $availability);
    }
}