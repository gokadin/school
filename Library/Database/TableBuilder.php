<?php namespace Library\Database;

use Database\Tables;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Library\Facades\DB;

class TableBuilder extends Tables
{
    protected $db;
    protected $blueprints;

    public function __construct($db)
    {
        $this->db = $db;

        $functions = get_class_methods('Database\Tables');

        foreach ($functions as $function)
        {
            $blueprint = $this->$function();
            $blueprint->setTable($function);
            $this->blueprints[] = $blueprint;
        }

        $this->buildTables();
    }

    public function getBlueprint($modelName)
    {
        foreach ($this->blueprints as $blueprint)
        {
            if ($blueprint->modelName() == $modelName)
                return $blueprint;
        }

        throw new RuntimeException('Table schema for '.$modelName.' does not exist.');
    }

    protected function buildTables()
    {
        foreach ($this->blueprints as $blueprint)
            $this->buildTable($blueprint);
    }

    protected function buildTable($blueprint)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '.$blueprint->table().' (';

        foreach ($blueprint->columns() as $column)
            $sql .= $this->buildColumn($column);
        $sql = substr($sql, 0, -3);

        foreach ($blueprint->columns() as $column)
        {
            if ($column->isUnique())
                $sql .= $this->buildUnique($column);
        }

        $sql .= ')';

        $this->db->query($sql);
    }

    protected function buildColumn($column)
    {
        $this->validateColumn($column);

        $str = $column->getName().' ';
        $str .= $this->getColumnTypeString($column->getType());
        if ($column->getSize() > 0)
            $str .= '('.$column->getSize().')';
        else if ($column->isPrimaryKey())
            $str .= '(11)';

        if ($column->isPrimaryKey())
        {
            $str .= ' UNSIGNED AUTO_INCREMENT PRIMARY KEY, ';
            return $str;
        }

        if (!$column->isNullable())
            $str .= ' NOT NULL';

        if ($column->getDefault() != null)
            $str .= ' DEFAULT '.$column->getDefault();

        $str .= ',  ';
        return $str;
    }

    protected function buildUnique($column)
    {
        return ', UNIQUE ('.$column->getName().')';
    }

    protected function getColumnTypeString($type)
    {
        switch ($type)
        {
            case 'integer':
                return 'INT';
            case 'double':
                return 'DOUBLE';
            case 'string':
                return 'VARCHAR';
            case 'boolean':
                return 'TINYINT';
            case 'datetime':
                return 'DATETIME';
            default:
                throw new RuntimeException('Unknown column type: '.$type);
                break;
        }
    }

    protected function validateColumn($column)
    {
        if ($column->isPrimaryKey() && $column->getType() != 'integer')
            throw new RuntimeException('Primary key '.$column->getName().' must be of integer type');
    }
}