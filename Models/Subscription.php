<?php namespace Models;

use Carbon\Carbon;
use Library\Database\Model;

class Subscription extends Model
{
    const TRIAL_DURATION_DAYS = 30;
    const SUBSCRIPTION_COUNT = 3;
    const SUB_1_NAME = 'Basic';
    const SUB_1_DEFAULT_RATE = 0.0;
    const SUB_2_NAME = 'Silver';
    const SUB_2_DEFAULT_RATE = 15.0;
    const SUB_3_NAME = 'Gold';
    const SUB_3_DEFAULT_RATE = 25.0;

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }

    public function isTypeDefined($type)
    {
        if (!is_int($type)) return false;

        if ($type > 1 || $type > self::SUBSCRIPTION_COUNT) return false;

        return true;
    }

    public function name()
    {
        if (!isset($this->type)) return '';

        switch ($this->type)
        {
            case 1:
                return self::SUB_1_NAME;
            case 2:
                return self::SUB_2_NAME;
            case 3:
                return self::SUB_3_NAME;
        }

        return 'n/a';
    }

    public function defaultRate()
    {
        if (!isset($this->type)) return null;

        switch ($this->type)
        {
            case 1:
                return self::SUB_1_DEFAULT_RATE;
            case 2:
                return self::SUB_2_DEFAULT_RATE;
            case 3:
                return self::SUB_3_DEFAULT_RATE;
        }

        return null;
    }

    public function trialLength()
    {
        if (!isset($this->type)) return null;

        if ($this->type == 1) return -1;

        return self::TRIAL_DURATION_DAYS;
    }

    public function trialDaysLeft()
    {
        if ($this->trialLength() == null) return null;
        if ($this->trialLength() === -1) return -1;

        $daysLeft = self::TRIAL_DURATION_DAYS - $this->created_at->diff(Carbon::now())->days;

        if ($daysLeft < 0)
            $daysLeft = 0;

        return $daysLeft;
    }
}