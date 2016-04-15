<?php

namespace App\Repositories;

use App\Domain\Calendar\Availability;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class AvailabilityRepository extends RepositoryBase
{
    public function getWeekNonDefault(Teacher $teacher, Carbon $weekStartDate)
    {
        return $teacher->weekAvailabilities()
            ->where('isDefault', '=', false)
            ->where('date', '=', $weekStartDate->toDateString())
            ->first();
    }

    public function getLastDefault(Teacher $teacher, Carbon $weekStartDate)
    {
        return $teacher->weekAvailabilities()
            ->where('isDefault', '=', true)
            ->where('date', '<=', $weekStartDate->toDateString())
            ->sortBy('date', false)
            ->first();
    }

    public function getDefault(Teacher $teacher)
    {
//        $results = $teacher->availabilities()->where('isDefault', '=', true)->sortBy('createdAt', false)->slice(0, 1);
//
//        if (sizeof($results) == 0)
//        {
//            return [];
//        }
//
//        $availability = $results[0];
//
//        return $teacher->availabilities()
//            ->where('isDefault', '=', true)
//            ->where('createdAt', '<=', Carbon::parse($availability->createdAt())->toDateString())
//            ->wehre('createdAt', '>=', Carbon::parse($availability->createdAt())->startOfWeek()->subDay()->toDateString())
//            ->toArray();
    }

    public function store(Availability $availability)
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