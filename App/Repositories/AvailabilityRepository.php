<?php

namespace App\Repositories;

use App\Domain\Calendar\Availability;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class AvailabilityRepository extends RepositoryBase
{
    public function range(Teacher $teacher, Carbon $fromDate, Carbon $toDate)
    {
        return $teacher->availabilities()->where('date', '>=', $fromDate)->where('date', '<=', $toDate)->toArray();
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