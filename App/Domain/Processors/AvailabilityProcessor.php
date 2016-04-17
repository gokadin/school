<?php

namespace App\Domain\Processors;

use App\Domain\Calendar\Availability;
use App\Domain\Calendar\WeekAvailability;
use Carbon\Carbon;

class AvailabilityProcessor
{
    public function extractJsonData(WeekAvailability $weekAvailability): array
    {
        $availabilities = [];

        foreach ($weekAvailability->availabilities() as $availability)
        {
            $availabilityObject = new Availability(
                Carbon::parse($availability['date']),
                $availability['startTime'],
                $availability['endTime']
            );
            $availabilityObject->setUniqueId($availability['uniqueId']);

            $availabilities[] = $availabilityObject;
        }

        return $availabilities;
    }
}