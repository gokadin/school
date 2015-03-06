<?php namespace Models;

use Library\Model;

class Students extends Model
{
    public function __construct($dao)
    {
        parent::__construct($dao, 'students');

        $this->setEntityName('student');
        $this->init();
    }

    public function init()
    {
        //$this->query('DROP TABLE students');
        $this->query('CREATE TABLE IF NOT EXISTS '.$this->tableName
                .'(id INT(11) unsigned NOT NULL AUTO_INCREMENT, '
                .'user_id INT(11) NOT NULL, '
                .'teacher_id INT(11) NOT NULL, '
                .'activity_id INT(11) NOT NULL, '
                .'PRIMARY KEY(id))');
    }
}
