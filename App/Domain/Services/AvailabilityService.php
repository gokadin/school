<?php

namespace App\Domain\Services;

use App\Domain\Calendar\Availability;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class AvailabilityService extends Service
{
    public function range(Teacher $teacher, Carbon $fromDate, Carbon $toDate)
    {
        return $this->repository->of(Availability::class)->range($teacher, $fromDate, $toDate);
    }

    public function store(Availability $availability)
    {
        return $this->repository->of(Availability::class)->store($availability);
    }

    public function update(Teacher $teacher, array $updated)
    {
        $availability = $teacher->availabilities()->find($updated['id']);
        $availability->setDate(Carbon::parse($updated['date']));
        $availability->setStartTime($updated['startTime']);
        $availability->setEndTime($updated['endTime']);

        $this->repository->of(Availability::class)->update($availability);
    }
}