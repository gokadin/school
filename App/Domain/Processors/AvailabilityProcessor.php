<?php

namespace App\Domain\Processors;

use App\Domain\Calendar\Availability;
use App\Domain\Calendar\WeekAvailability;
use Carbon\Carbon;

class AvailabilityProcessor
{
    public function extractJsonData(WeekAvailability $weekAvailability, Carbon $realWeekStartDate): array
    {
        $availabilities = [];

        foreach ($weekAvailability->availabilities() as $availability)
        {
            $availabilityObject = new Availability(
                $realWeekStartDate->copy()->addDays(Carbon::parse($availability['date'])->dayOfWeek),
                $availability['startTime'],
                $availability['endTime']
            );
            $availabilityObject->setUniqueId($availability['uniqueId']);

            $availabilities[] = $availabilityObject;
        }

        return $availabilities;
    }
}