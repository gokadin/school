<?php namespace Models;

use Library\Database\Model;

class Activity extends Model
{
    public function students()
    {
        return $this->belongsToMany('Student');
    }

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }

    public function periodToString()
    {
        if (!isset($this->period))
            return 'n/a';

        switch ($this->period)
        {
            case 999:
                return 'lesson';
            case 1:
                return 'month';
            case 2:
                return '2 months';
            case 3:
                return '3 months';
            case 4:
                return '4 months';
            case 5:
                return '5 months';
            case 6:
                return '6 months';
            case 7:
                return '7 months';
            case 8:
                return '8 months';
            case 9:
                return '9 months';
            case 10:
                return '10 months';
            case 11:
                return '11 months';
            case 12:
                return '12 months';
            case 15:
                return '15 mins';
            case 30:
                return '30 mins';
            case 45:
                return '45 mins';
            case 100:
                return '1 hour';
            case 115:
                return '1 hour 15 mins';
            case 130:
                return '1 hour 30 mins';
            case 145:
                return '1 hour 45 mins';
            case 200:
                return '2 hours';
        }
    }
}
