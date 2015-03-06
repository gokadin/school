<?php namespace Models;

use Library\Model;

class Users extends Model
{
    public function __construct($dao)
    {
        parent::__construct($dao, 'users');

        $this->setEntityName('user');
        $this->init();
    }

    public function init()
    {
        $this->query('DROP TABLE users');
        $this->query('CREATE TABLE IF NOT EXISTS '.$this->tableName
                .'(id INT(11) unsigned NOT NULL AUTO_INCREMENT, '
                .'school_id INT(11) NOT NULL, '
                .'first_name VARCHAR(32) NOT NULL, '
                .'last_name VARCHAR(32) NOT NULL, '
                .'email VARCHAR(32) NOT NULL, '
                .'password VARCHAR(32) NOT NULL, '
                .'phone VARCHAR(32), '
                .'type INT(11) NOT NULL, '
                .'active TINYINT(1), '
                .'date_created DATETIME NOT NULL, '
                .'timestamp DATETIME NOT NULL, '
                .'PRIMARY KEY(id))');
    }
}
