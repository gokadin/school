<?php namespace Models;

use Library\Model;

class Teachers extends Model
{
    public function __construct($dao)
    {
        parent::__construct($dao, 'teachers');

        $this->setEntityName('teacher');
        $this->init();
    }

    public function init()
    {
        //$this->query('DROP TABLE teachers');
        $this->query('CREATE TABLE IF NOT EXISTS '.$this->tableName
                .'(id INT(11) unsigned NOT NULL AUTO_INCREMENT, '
                .'user_id INT(11) NOT NULL, '
                .'plan_id INT(11) NOT NULL, '
                .'PRIMARY KEY(id))');
    }
}
