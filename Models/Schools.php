<?php namespace Models;

use Library\Model;

class Schools extends Model
{
    public function __construct($dao)
    {
        parent::__construct($dao, 'schoold');

        $this->setEntityName('school');
        $this->init();
    }

    public function init()
    {
        //$this->query('DROP TABLE schools');
        $this->query('CREATE TABLE IF NOT EXISTS '.$this->tableName
                .'(id INT(11) unsigned NOT NULL AUTO_INCREMENT, '
                .'teacher_id INT(11) NOT NULL, '
                .'name VARCHAR(32) NOT NULL, '
                .'date_created DATETIME NOT NULL, '
                .'timestamp DATETIME NOT NULL, '
                .'PRIMARY KEY(id))');
    }
}
