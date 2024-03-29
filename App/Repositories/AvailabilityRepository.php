<?php

namespace App\Repositories;

use App\Domain\Calendar\Availability;
use App\Domain\Calendar\WeekAvailability;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class AvailabilityRepository extends RepositoryBase
{
    public function getWeekNonDefault(Teacher $teacher, Carbon $weekStartDate)
    {
        return $teacher->weekAvailabilities()
            ->where('isDefault', '=', false)
            ->where('weekStartDate', '=', $weekStartDate->toDateString())
            ->first();
    }

    public function getLastDefault(Teacher $teacher, Carbon $weekStartDate)
    {
        return $teacher->weekAvailabilities()
            ->where('isDefault', '=', true)
            ->where('weekStartDate', '<=', $weekStartDate->toDateString())
            ->sortBy('weekStartDate', false)
            ->first();
    }

    public function getCurrentDefault(Teacher $teacher, Carbon $weekStartDate)
    {
        return $teacher->weekAvailabilities()
            ->where('isDefault', '=', true)
            ->where('weekStartDate', '=', $weekStartDate->toDateString())
            ->first();
    }

    public function removeFutureTemplates(Carbon $weekStartDate)
    {
        $this->dm->queryBuilder()->table('week_availabilities')
            ->where('isDefault', '=', true)
            ->where('weekStartDate', '>=', $weekStartDate->copy()->addWeek()->toDateString())
            ->delete();
    }

    public function store(WeekAvailability $availability)
    {
        $this->dm->persist($availability);

        $this->dm->flush();

        return $availability->getId();
    }

    public function update($availability)
    {
        $this->dm->flush();
    }

    public function delete($availability)
    {
        $this->dm->delete($availability);

        $this->dm->flush();
    }
}