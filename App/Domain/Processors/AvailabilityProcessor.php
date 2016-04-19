<?php

namespace App\Domain\Processors;

use App\Domain\Calendar\Availability;
use App\Domain\Calendar\WeekAvailability;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class AvailabilityProcessor
{
    public function getRealWeekStartDate(Carbon $weekStartDate)
    {
        return $weekStartDate->dayOfWeek == Carbon::SUNDAY
            ? $weekStartDate->copy()
            : $weekStartDate->copy()->startOfWeek()->subDay();
    }

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

    public function copyFromDefaultTemplate(WeekAvailability $default, Carbon $copyDate)
    {
        $weekAvailability = $this->copyWeekAvailability($default, $copyDate);

        $availabilities = $weekAvailability->availabilities();
        foreach ($availabilities as &$availability)
        {
            $availability['date'] = $copyDate->copy()->addDays(Carbon::parse($availability['date'])->dayOfWeek)->toDateString();
        }

        $weekAvailability->setJsonData(json_encode($availabilities));

        return $weekAvailability;
    }

    public function copyToDefaultTemplate(WeekAvailability $weekAvailability)
    {
        $default = $this->copyWeekAvailability($weekAvailability, Carbon::parse($weekAvailability->weekStartDate()));
        $default->setAsDefault();

        return $default;
    }

    private function copyWeekAvailability(WeekAvailability $weekAvailability, Carbon $newDate)
    {
        $copy = new WeekAvailability($weekAvailability->teacher(), $newDate);
        $copy->setJsonData($weekAvailability->jsonData());
        $copy->setNextAvailabilityId($weekAvailability->nextAvailabilityId());

        return $copy;
    }
}