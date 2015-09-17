<?php

namespace Library\DataMapper\Database\Drivers;

use Library\DataMapper\Mapping\Metadata;
use PDO;

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

        $str = ' WHERE';

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

    public function insert($str, $data)
    {
        $stmt = $this->dao->prepare($str);
        $stmt->execute($data);

        return $this->dao->lastInsertId();
    }

    public function update($str, $data)
    {
        $stmt = $this->dao->prepare($str);
        $stmt->execute($data);
    }

    public function delete($str)
    {
        return $this->dao->exec($str);
    }

    public function createTable(Metadata $metadata)
    {
        $str = 'CREATE TABLE IF NOT EXISTS '.$metadata->table();

        $primaryKeyStr = '';
        $columnsStr = [];
        foreach ($metadata->columns() as $column)
        {
            if ($column->isPrimaryKey())
            {
                $primaryKeyStr = $column->name();
                $primaryKeyStr .= ' '.$this->getColumnTypeString($column->type());
                $primaryKeyStr .= '('.$column->size().')';
                $primaryKeyStr .= ' UNSIGNED AUTO_INCREMENT PRIMARY KEY';
                continue;
            }

            $columnStr = $column->name();
            $columnStr .= ' '.$this->getColumnTypeString($column->type());
            if ($column->type() != 'datetime' && $column->type() != 'text')
            {
                if ($column->size() > 0)
                {
                    $columnStr .= '('.$column->size();
                    if ($column->type() == 'decimal')
                        $columnStr .= ', '.$column->precision();
                    $columnStr .= ')';
                }
            }
            if (!$column->isNullable())
                $columnStr .= ' NOT NULL';
            if ($column->isDefault())
            {
                $columnStr .= ' DEFAULT';
                is_string($column->getDefaultValue())
                    ? $columnStr .= ' \''.$column->getDefaultValue().'\''
                    : $columnStr .= ' '.$column->getDefaultValue();
            }

            $columnsStr[] = $columnStr;
        }

        $str .= ' (';
        $str .= $primaryKeyStr.',';
        $str .= ' '.implode(', ', $columnsStr);

        foreach ($metadata->columns() as $column)
        {
            if ($column->isUnique())
            {
                $str .= ', UNIQUE ('.$column->name().')';
            }
        }

        $str .= ')';

        return $this->dao->exec($str);
    }

    public function dropTable($table)
    {
        $this->dao->exec('DROP TABLE '.$table);
    }

    protected function getColumnTypeString($type)
    {
        switch ($type)
        {
            case 'integer':
                return 'INT';
            case 'decimal':
                return 'DECIMAL';
            case 'string':
                return 'VARCHAR';
            case 'text':
                return 'TEXT';
            case 'boolean':
                return 'TINYINT';
            case 'datetime':
                return 'DATETIME';
            default:
                throw new RuntimeException('Unknown column type: '.$type);
                break;
        }
    }
}