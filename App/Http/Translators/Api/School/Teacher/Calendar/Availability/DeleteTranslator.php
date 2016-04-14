<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use Library\Http\Request;

class DeleteTranslator extends AvailabilityTranslator
{
    public function translateRequest(Request $request)
    {
        $this->availabilityService->delete($this->user, $request->id);
    }
}