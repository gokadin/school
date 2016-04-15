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

    /** @Column(type="boolean", defaultValue="false") */
    private $isDefault;

    /** @Column(type="text", defaultValue="[]") */
    private $jsonData;

    public function __construct(Teacher $teacher, $date)
    {
        $this->teacher = $teacher;
        $this->date = $date;
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
    }
}