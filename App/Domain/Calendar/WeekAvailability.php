<?php

namespace App\Domain\Calendar;

use App\Domain\Users\Teacher;
use Carbon\Carbon;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="week_availabilities")
 */
class WeekAvailability
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @BelongsTo(target="\App\Domain\Users\Teacher") */
    private $teacher;

    /** @Column(type="datetime") */
    private $weekStartDate;

    /** @Column(type="boolean", default="false") */
    private $isDefault;

    /** @Column(type="text") */
    private $jsonData;

    /** @Column(type="integer", default="1") */
    private $nextAvailabilityId;

    /**
     * @var array
     */
    private $decodedJsonData;

    public function __construct(Teacher $teacher, $weekStartDate)
    {
        $this->teacher = $teacher;
        $this->weekStartDate = $weekStartDate;

        $this->jsonData = '[]';
        $this->nextAvailabilityId = 1;
    }

    /**
     * @return Teacher
     */
    public function teacher()
    {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function weekStartDate()
    {
        return $this->weekStartDate;
    }

    public function setWeekStartDate(Carbon $weekStartDate)
    {
        $this->weekStartDate = $weekStartDate;
    }

    public function isDefault()
    {
        return $this->isDefault;
    }

    public function setAsDefault()
    {
        return $this->isDefault = true;
    }

    public function jsonData()
    {
        return $this->jsonData;
    }

    public function setJsonData(string $jsonData)
    {
        $this->jsonData = $jsonData;

        $this->decodedJsonData = null;
    }

    public function nextAvailabilityId()
    {
        return $this->nextAvailabilityId;
    }

    public function setNextAvailabilityId(int $id)
    {
        $this->nextAvailabilityId = $id;
    }

    public function availabilities()
    {
        $this->decodeAvailabilitiesIfNull();

        return $this->decodedJsonData;
    }

    public function addAvailability(Availability $availability)
    {
        $this->decodeAvailabilitiesIfNull();

        $availability->setUniqueId($this->nextAvailabilityId);
        $this->decodedJsonData[] = $availability->jsonSerialize();

        $this->jsonData = json_encode($this->decodedJsonData);

        $this->nextAvailabilityId++;
    }

    public function removeAvailability(Availability $availability)
    {
        $this->decodeAvailabilitiesIfNull();

        foreach ($this->decodedJsonData as $key => $value)
        {
            if ($value['uniqueId'] == $availability->uniqueId())
            {
                unset($this->decodedJsonData[$key]);

                break;
            }
        }

        $this->jsonData = json_encode($this->decodedJsonData);
    }

    private function decodeAvailabilitiesIfNull()
    {
        if (is_null($this->decodedJsonData))
        {
            $this->decodedJsonData = json_decode($this->jsonData, true);
        }
    }
}