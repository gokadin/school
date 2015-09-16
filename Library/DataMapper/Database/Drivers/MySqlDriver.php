<?php

namespace Library\DataMapper\Database\Drivers;

class MySqlDriver
{
    const NAME = 'mysql';

    protected $dao;

    public function __construct($config)
    {
        $this->dao = new PDO('mysql:host='.$config['host'].';dbname='.$config['database'],
            $config['username'],
            $config['password']);

        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function name()
    {
        return self::NAME;
    }

    public function execute($str)
    {
        $stmt = $this->dao->prepare($str);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buildWheres(array $wheres)
    {
        if (sizeof($wheres) == 0)
        {
            return '';
        }

        $str = 'WHERE';

        for ($i = 0; $i < sizeof($wheres); $i++)
        {
            if ($i > 0)
            {
                $str .= ' '.$wheres[$i]['link'];
            }

            $str .= ' '.$wheres[$i]['var'];
            $str .= ' '.$wheres[$i]['operator'];

            $value = $wheres[$i]['value'];
            if (trim($wheres[$i]['operator']) != 'in')
            {
                if (substr($value, 0, 1) != '\'' && substr($value, -1) != '\'')
                {
                    $value = '\''.$value.'\'';
                }
            }

            $str .= ' '.$value;
        }

        return $str;
    }
}