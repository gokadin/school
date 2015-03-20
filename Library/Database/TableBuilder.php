<?php namespace Library\Database;

use Database\Tables;
use Library\Config;
use Symfony\Component\Yaml\Exception\RuntimeException;

class TableBuilder extends Tables
{
    protected $db;
    protected $tables;

    public function __construct($db)
    {
        $this->db = $db;

        if (Config::get('testing') == 'true')
            $functions = get_class_methods('\\Tests\\FrameworkTest\\Database\\Tables');
        else
            $functions = get_class_methods('Database\\Tables');

        if (Config::get('testing') == 'true')
        {
            $testTablesName = '\\Tests\\FrameworkTest\\Database\\Tables';
            $testTables = new $testTablesName();
            foreach ($functions as $function)
            {
                $table = $testTables->$function();
                $table->setTable($function);
                $this->tables[] = $table;
            }
        }
        else
        {
            foreach ($functions as $function)
            {
                $table = $this->$function();
                $table->setTable($function);
                $this->tables[] = $table;
            }
        }

        $this->buildTables();
    }

    public function getTable($modelName)
    {
        foreach ($this->tables as $table)
        {
            if (strcasecmp($table->modelName(), $modelName) == 0)
                return $table;
        }

        throw new RuntimeException('Table schema for '.$modelName.' does not exist.');
    }

    protected function buildTables()
    {
        foreach ($this->tables as $table)
            $this->buildTable($table);
    }

    protected function buildTable($table)
    {
        $sql = 'CREATE TABLE IF NOT EXISTS '.$table->tableName().' (';

        foreach ($table->columns() as $column)
            $sql .= $this->buildColumn($column);
        $sql = substr($sql, 0, -3);

        foreach ($table->columns() as $column)
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
            case 'decimal':
                return 'DECIMAL';
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