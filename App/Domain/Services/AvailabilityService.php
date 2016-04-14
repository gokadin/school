<?php

namespace App\Domain\Services;

use App\Domain\Calendar\Availability;
use App\Domain\Processors\AvailabilityProcessor;
use App\Domain\Users\Teacher;
use App\Repositories\AvailabilityRepository;
use App\Repositories\Repository;
use Carbon\Carbon;
use Library\Events\EventManager;

class AvailabilityService extends Service
{
    /**
     * @var AvailabilityRepository
     */
    private $availabilityRepository;

    public function __construct(EventManager $eventManager, Repository $repository)
    {
        parent::__construct($eventManager, $repository);

        $this->availabilityRepository = $repository->of(Availability::class);
    }

    public function fetch(Teacher $teacher, Carbon $weekStartDate): array
    {
        $availabilityProcessor = new AvailabilityProcessor();

        $weekAvailability = $this->availabilityRepository->getWeekNonDefault($teacher, $weekStartDate);

        return is_null($weekAvailability) ?: $availabilityProcessor->extractJsonData($weekAvailability);

        $defaultAvailability = $this->availabilityRepository->getLastDefault($teacher, $weekStartDate);

        return is_null($defaultAvailability) ? [] : $availabilityProcessor->extractFromJson($defaultAvailability);
    }

    public function store(Availability $availability)
    {
        return $this->availabilityRepository->store($availability);
    }

    public function update(Teacher $teacher, array $updated)
    {
        $availability = $teacher->availabilities()->find($updated['id']);
        $availability->setDate(Carbon::parse($updated['date']));
        $availability->setStartTime($updated['startTime']);
        $availability->setEndTime($updated['endTime']);

        $this->availabilityRepository->update($availability);
    }

    public function destroy(Teacher $teacher, int $id)
    {
        $this->availabilityRepository->delete($teacher->availabilities()->find($id));
    }
}