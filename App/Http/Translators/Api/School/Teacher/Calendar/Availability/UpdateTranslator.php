<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use Library\Http\Request;

class UpdateTranslator extends AvailabilityTranslator
{
    public function translateRequest(Request $request)
    {
        $this->availabilityService->update($this->user, $request->all());
    }
}