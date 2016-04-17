<?php

namespace App\Domain\Services;

use App\Domain\Calendar\Availability;
use App\Domain\Calendar\WeekAvailability;
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

        if (!is_null($weekAvailability))
        {
            return $availabilityProcessor->extractJsonData($weekAvailability);
        }

        $defaultAvailability = $this->availabilityRepository->getLastDefault($teacher, $weekStartDate);

        return is_null($defaultAvailability) ? [] : $availabilityProcessor->extractJsonData($defaultAvailability);
    }

    public function store(Teacher $teacher, Availability $availability)
    {
        $weekStartDate = $availability->date()->dayOfWeek == Carbon::SUNDAY
            ? $availability->date()->copy()
            : $availability->date()->copy()->startOfWeek()->subDay();

        $weekAvailability = $this->availabilityRepository->getWeekNonDefault($teacher, $weekStartDate);

        if (!is_null($weekAvailability))
        {
            $weekAvailability->addAvailability($availability);
            $this->availabilityRepository->update($weekAvailability);

            return $availability->uniqueId();
        }

        $defaultAvailability = $this->availabilityRepository->getLastDefault($teacher, $weekStartDate);
        $weekAvailability = new WeekAvailability($teacher, $weekStartDate);

        if (!is_null($defaultAvailability))
        {
            $weekAvailability->setJsonData($defaultAvailability->jsonData());
            $weekAvailability->setNextAvailabilityId($defaultAvailability->nextAvailabilityId());
            $weekAvailability->addAvailability($availability);
            $this->availabilityRepository->store($weekAvailability);

            return $availability->uniqueId();
        }

        $weekAvailability->addAvailability($availability);
        $this->availabilityRepository->store($weekAvailability);

        return $availability->uniqueId();
    }

    public function update(Teacher $teacher, Availability $availability)
    {
        $weekStartDate = $availability->date()->dayOfWeek == Carbon::SUNDAY
            ? $availability->date()->copy()
            : $availability->date()->copy()->startOfWeek()->subDay();

        $weekAvailability = $this->availabilityRepository->getWeekNonDefault($teacher, $weekStartDate);

        if (!is_null($weekAvailability))
        {
            $availabilities = $weekAvailability->availabilities();
            foreach ($availabilities as &$a)
            {
                if ($a['uniqueId'] == $availability->uniqueId())
                {
                    $a['date'] = $availability->date()->toDateString();
                    $a['startTime'] = $availability->startTime();
                    $a['endTime'] = $availability->endTime();

                    break;
                }
            }

            $weekAvailability->setJsonData(json_encode($availabilities));

            $this->availabilityRepository->update($weekAvailability);

            return;
        }

        $defaultAvailability = $this->availabilityRepository->getLastDefault($teacher, $weekStartDate);
        $weekAvailability = new WeekAvailability($teacher, $weekStartDate);
        $weekAvailability->setJsonData($defaultAvailability->jsonData());
        $weekAvailability->setNextAvailabilityId($defaultAvailability->nextAvailabilityId());

        $availabilities = $weekAvailability->availabilities();
        foreach ($availabilities as &$a)
        {
            if ($a['uniqueId'] == $availability->uniqueId())
            {
                $a['date'] = $availability->date()->toDateString();
                $a['startTime'] = $availability->startTime();
                $a['endTime'] = $availability->endTime();

                continue;
            }

            $a['date'] = $weekStartDate->copy()->addDays(Carbon::parse($a['date'])->dayOfWeek)->toDateString();
        }

        $weekAvailability->setJsonData(json_encode($availabilities));

        $this->availabilityRepository->store($weekAvailability);
    }

    public function destroy(Teacher $teacher, int $id)
    {
        $this->availabilityRepository->delete($teacher->availabilities()->find($id));
    }
}