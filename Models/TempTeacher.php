<?php namespace Models;

use Library\Database\Model;

class TempTeacher extends Model
{
	public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
