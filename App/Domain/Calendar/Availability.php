<?php

namespace App\Domain\Calendar;

use Carbon\Carbon;
use JsonSerializable;

class Availability implements JsonSerializable
{
    /**
     * @var string
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

        $this->generateUniqueId();
    }

    public function generateUniqueId()
    {
        $this->uniqueId = $this->date->dayOfWeek.'-'.$this->startTime.'-'.$this->endTime;
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