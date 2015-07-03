<?php namespace Models;

use Library\Database\Model;

class TempTeacher extends Model
{
    protected $fillable = [
        'subscription_id',
        'type',
        'first_name',
        'last_name',
        'email',
        'confirmation_code'
    ];

	public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
