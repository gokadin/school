<?php

namespace Library\Database\Drivers;

use PDO;

class PdoDatabaseDriver implements IDatabaseDriver
{
    protected $dao;
    protected $databaseName;
    protected $select = null;
    protected $wheres = [];

    public function __construct($settings)
    {
        $this->databaseName = $settings['database'];
        $this->dao = new PDO('mysql:host='.$settings['host'].';dbname='.$this->databaseName,
            $settings['username'],
            $settings['password']);

        $this->dao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function insert($table, array $data)
    {
        $str = 'INSERT INTO '.$table;
        $str .= ' ('.implode(',', array_keys($data)).')';

        array_walk($data, function(&$key, $value) {
            $key = ':'.$key;
        });

        $str .= ' VALUES('.implode(',', array_keys($data)).')';

        $stmt = $this->dao->prepare($str);
        $stmt->execute($data);
    }

    public function select($table)
    {
        return $this;
    }

    public function where($var, $operator, $value = null)
    {
        $this->addWhere($var, $operator, $value, 'AND');
        return $this;
    }

    public function andWhere($var, $operator, $value = null)
    {
        $this->addWhere($var, $operator, $value, 'AND');
        return $this;
    }

    public function orWhere($var, $operator, $value = null)
    {
        $this->addWhere($var, $operator, $value, 'OR');
        return $this;
    }

    public function get(array $fields = ['*'])
    {
        if (is_null($this->select))
        {
            return [];
        }

        $str = ' SELECT';
        $str .= ' '.implode(',', $fields);
        $str .= ' FROM '.$this->select;
        $str .= $this->buildWheres();

        $stmt = $this->dao->prepare($str);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function dropAll()
    {
        $this->dao->exec('DROP DATABASE '.$this->databaseName);
        $this->dao->exec('CREATE DATABASE '.$this->databaseName);
    }

    public function beginTransaction()
    {
        $this->dao->beginTransaction();
    }

    public function commit()
    {
        $this->dao->commit();
    }

    public function rollBack()
    {
        $this->dao->rollBack();
    }

    protected function addWhere($var, $operator, $value, $link)
    {
        if (is_null($value))
        {
            $value = $operator;
            $operator = '=';
        }

        $this->wheres[] = [
            'var' => $var,
            'operator' => $operator,
            'value' => $value,
            'link' => $link
        ];
    }

    protected function buildWheres()
    {
        if (sizeof($this->wheres) == 0)
        {
            return '';
        }

        $str = ' WHERE';

        for ($i = 0; $i < sizeof($this->wheres); $i++)
        {
            if ($i > 0)
            {
                $str .= ' '.$this->wheres[$i]['link'];
            }

            $str .= ' '.$this->wheres[$i]['var'];
            $str .= ' '.$this->wheres[$i]['operator'];
            $str .= ' '.$this->wheres[$i]['value'];
        }

        return $str;
    }
}