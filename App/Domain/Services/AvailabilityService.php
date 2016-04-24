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

    /**
     * @var AvailabilityProcessor
     */
    private $availabilityProcessor;

    public function __construct(EventManager $eventManager, Repository $repository)
    {
        parent::__construct($eventManager, $repository);

        $this->availabilityRepository = $repository->of(Availability::class);
        $this->availabilityProcessor = new AvailabilityProcessor();
    }

    public function fetch(Teacher $teacher, Carbon $weekStartDate): array
    {
        $weekAvailability = $this->availabilityRepository->getWeekNonDefault($teacher, $weekStartDate);

        if (!is_null($weekAvailability))
        {
            return $this->availabilityProcessor->extractJsonData($weekAvailability, $weekStartDate);
        }

        $defaultAvailability = $this->availabilityRepository->getLastDefault($teacher, $weekStartDate);

        return is_null($defaultAvailability) ? [] : $this->availabilityProcessor->extractJsonData($defaultAvailability, $weekStartDate);
    }

    public function store(Teacher $teacher, Availability $availability)
    {
        $weekStartDate = $this->availabilityProcessor->getRealWeekStartDate($availability->date());

        $weekAvailability = $this->availabilityRepository->getWeekNonDefault($teacher, $weekStartDate);

        if (!is_null($weekAvailability))
        {
            $weekAvailability->addAvailability($availability);
            $this->availabilityRepository->update($weekAvailability);

            return $availability->uniqueId();
        }

        $defaultAvailability = $this->availabilityRepository->getLastDefault($teacher, $weekStartDate);

        if (!is_null($defaultAvailability))
        {
            $weekAvailability = $this->availabilityProcessor->copyFromDefaultTemplate($defaultAvailability, $weekStartDate);

            $weekAvailability->addAvailability($availability);
            $this->availabilityRepository->store($weekAvailability);

            return $availability->uniqueId();
        }

        $weekAvailability = new WeekAvailability($teacher, $weekStartDate);
        $weekAvailability->addAvailability($availability);
        $this->availabilityRepository->store($weekAvailability);

        return $availability->uniqueId();
    }

    public function update(Teacher $teacher, Availability $availability)
    {
        $weekStartDate = $this->availabilityProcessor->getRealWeekStartDate($availability->date());

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

        $weekAvailability = $this->availabilityProcessor->copyFromDefaultTemplate(
            $this->availabilityRepository->getLastDefault($teacher, $weekStartDate), $weekStartDate);

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
        $this->availabilityRepository->store($weekAvailability);
    }

    public function destroy(Teacher $teacher, Availability $availability)
    {
        $weekStartDate = $this->availabilityProcessor->getRealWeekStartDate($availability->date());

        $weekAvailability = $this->availabilityRepository->getWeekNonDefault($teacher, $weekStartDate);

        if (!is_null($weekAvailability))
        {
            $weekAvailability->removeAvailability($availability);
            $this->availabilityRepository->update($weekAvailability);

            return;
        }

        $weekAvailability = $this->availabilityProcessor->copyFromDefaultTemplate(
            $this->availabilityRepository->getLastDefault($teacher, $weekStartDate), $weekStartDate);

        $weekAvailability->removeAvailability($availability);
        $this->availabilityRepository->store($weekAvailability);
    }

    public function applyToFutureWeeks(Teacher $teacher, Carbon $weekStartDate)
    {
        $weekStartDate = $this->availabilityProcessor->getRealWeekStartDate($weekStartDate);

        $weekAvailability = $this->availabilityRepository->getWeekNonDefault($teacher, $weekStartDate);

        if (is_null($weekStartDate))
        {
            return false;
        }

        $default = $this->availabilityRepository->getCurrentDefault($teacher, $weekStartDate);

        if (is_null($default))
        {
            $default = $this->availabilityProcessor->copyToDefaultTemplate($weekAvailability);
            $this->availabilityRepository->store($default);
            return true;
        }

        $default->setJsonData($weekAvailability->jsonData());
        $default->setNextAvailabilityId($weekAvailability->nextAvailabilityId());
        $this->availabilityRepository->update($default);
    }
}