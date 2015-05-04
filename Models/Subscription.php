<?php namespace Models;

use Carbon\Carbon;
use Library\Database\Model;

class Subscription extends Model
{
    const TRIAL_DURATION_DAYS = 30;
    const SUBSCRIPTION_COUNT = 4;
    const SUB_1_NAME = 'Basic';
    const SUB_1_DEFAULT_RATE = 0.0;
    const SUB_1_NUM_STUDENTS = 5;
    const SUB_1_STORAGE = 1;
    const SUB_2_NAME = 'Silver';
    const SUB_2_DEFAULT_RATE = 15.0;
    const SUB_2_NUM_STUDENTS = 20;
    const SUB_2_STORAGE = 4;
    const SUB_3_NAME = 'Gold';
    const SUB_3_DEFAULT_RATE = 25.0;
    const SUB_3_NUM_STUDENTS = 50;
    const SUB_3_STORAGE = 7;
    const SUB_4_NAME = 'Platinum';
    const SUB_4_DEFAULT_RATE = 25.0;
    const SUB_4_NUM_STUDENTS = -1;
    const SUB_4_STORAGE = 10;

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
    
    public function name($type = null)
    {
        if ($type == null)
        {
            if (isset($this->type))
                $type = $this->type;
            else
                return '';
        }
        
        if ($type < 1 || $type > self::SUBSCRIPTION_COUNT) return '';

        switch ($type)
        {
            case 1:
                return self::SUB_1_NAME;
            case 2:
                return self::SUB_2_NAME;
            case 3:
                return self::SUB_3_NAME;
            case 4:
                return self::SUB_4_NAME;
        }

        return 'n/a';
    }

    public function defaultRate($type = null)
    {
        if ($type == null)
        {
            if (isset($this->type))
                $type = $this->type;
            else
                return null;
        }
        
        if ($type < 1 || $type > self::SUBSCRIPTION_COUNT) return null;

        switch ($type)
        {
            case 1:
                return self::SUB_1_DEFAULT_RATE;
            case 2:
                return self::SUB_2_DEFAULT_RATE;
            case 3:
                return self::SUB_3_DEFAULT_RATE;
            case 4:
                return self::SUB_4_DEFAULT_RATE;
        }

        return null;
    }

    public function storage($type = null)
    {
        if ($type == null)
        {
            if (isset($this->type))
                $type = $this->type;
            else
                return null;
        }
        
        if ($type < 1 || $type > self::SUBSCRIPTION_COUNT) return null;

        switch ($type)
        {
            case 1:
                return self::SUB_1_STORAGE;
            case 2:
                return self::SUB_2_STORAGE;
            case 3:
                return self::SUB_3_STORAGE;
            case 4:
                return self::SUB_4_STORAGE;
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

        $created_at = new Carbon($this->created_at);
        $daysLeft = self::TRIAL_DURATION_DAYS - $created_at->diff(Carbon::now())->days;

        if ($daysLeft < 0)
            $daysLeft = 0;

        return $daysLeft;
    }
}