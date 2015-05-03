<?php namespace Models;

use Library\Database\Model;

class TempUser extends Model
{
	public function name()
    {
        return $this->first_name.' '.$this->last_name;
    }
}
