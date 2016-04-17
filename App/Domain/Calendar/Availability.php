<?php

namespace App\Domain\Calendar;

use Carbon\Carbon;
use JsonSerializable;

class Availability implements JsonSerializable
{
    /**
     * @var int
     */
    private $uniqueId;

    /**
     * @var Carbon
     */
    private $date;

    /**
     * @var int
     */
    private $startTime;

    /**
     * @var int
     */
    private $endTime;

    public function __construct(Carbon $date, int $startTime, int $endTime)
    {
        $this->date = $date;
        $this->startTime = $startTime;
        $this->endTime = $endTime;

        $this->uniqueId = 0;
    }

    public function uniqueId()
    {
        return $this->uniqueId;
    }

    public function setUniqueId(int $uniqueId)
    {
        $this->uniqueId = $uniqueId;
    }

    public function date()
    {
        return $this->date;
    }

    public function setDate(Carbon $date)
    {
        $this->date = $date;
    }

    public function startTime()
    {
        return $this->startTime;
    }

    public function setStartTime(string $startTime)
    {
        $this->startTime = $startTime;
    }

    public function endTime()
    {
        return $this->endTime;
    }

    public function setEndTime(string $endTime)
    {
        $this->endTime = $endTime;
    }

    public function jsonSerialize()
    {
        return [
            'uniqueId' => $this->uniqueId,
            'date' => $this->date->toDateString(),
            'startTime' => $this->startTime,
            'endTime' => $this->endTime
        ];
    }
}