<?php namespace Models;

use Library\Model;

class Activities extends Model
{
    public function __construct($dao)
    {
        parent::__construct($dao, 'activities');

        $this->setEntityName('activity');
        $this->init();
    }

    public function init()
    {
        //$this->query('DROP TABLE activities');
        $this->query('CREATE TABLE IF NOT EXISTS '.$this->tableName
                .'(id INT(11) unsigned NOT NULL AUTO_INCREMENT, '
                .'teacher_id INT(11) NOT NULL, '
                .'name VARCHAR(32) NOT NULL, '
                .'rate DECIMAL(11) NOT NULL, '
                .'period INT(11) NOT NULL, '
                .'date_created DATETIME NOT NULL, '
                .'timestamp DATETIME NOT NULL, '
                .'PRIMARY KEY(id))');
    }
}
