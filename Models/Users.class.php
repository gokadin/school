<?php namespace Models;

use Library\Model;

class Users extends Model
{
    public function __construct($dao)
    {
        parent::__construct($dao);
        $this->tableName = 'users';

        $this->init();
    }

    public function init()
    {
        $this->query('CREATE TABLE IF NOT EXISTS '.$this->tableName
                .'(id INT(11) unsigned NOT NULL AUTO_INCREMENT, '
                .'venue_id INT(11) NOT NULL, '
                .'superior_id INT(11) NOT NULL, '
                .'name VARCHAR(32), '
                .'username VARCHAR(32) NOT NULL, '
                .'password VARCHAR(32) NOT NULL, '
                .'email VARCHAR(32) NOT NULL, '
                .'phone VARCHAR(32), '
                .'type INT(11) NOT NULL, '
                .'active TINYINT(1), '
                .'timestamp DATETIME NOT NULL, '
                .'PRIMARY KEY(id))');
    }
}
?>