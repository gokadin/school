<?php

namespace App\Http\Translators\Api\School\Teacher\Calendar\Availability;

use App\Domain\Calendar\Availability;
use Carbon\Carbon;
use Library\Http\Request;

class FetchTranslator extends AvailabilityTranslator
{
    public function translateRequest(Request $request): array
    {
        $weekStartDate = Carbon::parse($request->weekStartDate);
        if ($weekStartDate->dayOfWeek != Carbon::SUNDAY)
        {
            return false;
        }

        return $this->translateResponse($this->availabilityService->fetch($this->user, $weekStartDate));
    }

    private function translateResponse(array $data): array
    {
        return [
            'availabilities' => $this->transformer->of(Availability::class)->transform($data)
        ];
    }
}