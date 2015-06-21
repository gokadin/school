<?php namespace Library\Database;

use Library\Facades\DB;
use Symfony\Component\Yaml\Exception\RuntimeException;
use Carbon\Carbon;

class Model implements ModelQueryContract
{
    private $query;
    protected $vars = array();
    protected $tableName;
    protected $modelName;
    protected $modelDirectory;
    protected $primaryKeyName;
    private $columns;
    protected $columnNames;
    private $hasTimestamps;

    public function __construct(array $data = array())
    {
        if (\Library\Config::get('testing') == 'true')
            $this->modelDirectory = '\\Tests\\FrameworkTest\\Database\\Models\\';
        else
            $this->modelDirectory = '\\Models\\';

        $this->modelName = get_called_class();
        $this->modelName = substr($this->modelName, strrpos($this->modelName, '\\') + 1);
        $table = DB::getTable($this->modelName);
        $this->tableName = $table->tableName();
        $this->hasTimestamps = $table->hasTimestamps();
        foreach ($table->columns() as $column)
        {
            if ($column->isPrimaryKey())
                $this->primaryKeyName = $column->getName();
            else {
                $this->columns[] = $column;
                $this->columnNames[] = $column->getName();
            }
        }


        foreach ($data as $key => $value)
            $this->__set($key, $value);

        $this->query = new Query($this);
    }

    public function tableName()
    {
        return $this->tableName;
    }

    public function modelName()
    {
        return $this->modelName;
    }

    public function modelDirectory()
    {
        return $this->modelDirectory;
    }

    public function columnNames()
    {
        return $this->columnNames;
    }

    public function primaryKeyName()
    {
        return $this->primaryKeyName;
    }

    public function primaryKeyValue()
    {
        if (isset($this->vars[$this->primaryKeyName]))
            return $this->vars[$this->primaryKeyName];
    }

    public function defaultForeignKey()
    {
        return $this->camelCaseToUnderscore($this->modelName) . '_id';
    }

    public function hasTimestamps()
    {
        return $this->hasTimestamps;
    }

    public function __set($var, $value)
    {
        $this->vars[$var] = $value;
    }

    public function __get($var)
    {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
    }

    public function __isset($var)
    {
        if (isset($this->vars[$var]))
            return true;

        return false;
    }

    public function save()
    {
        if ($this->isMissingPrimaryKey())
            return $this->insert();
        else
            return $this->update();
    }

    private function update()
    {
        if ($this->isMissingRequiredColumn())
            throw new RuntimeException('A required column is missing from table ' . $this->tableName);

        $values = array();

        foreach ($this->vars as $key => $var)
        {
            if ($key == $this->primaryKeyName) continue;

            if ($this->hasColumn($key))
                $values[$key] = $var;
        }

        return $this->query->update($values);
    }

    public function touch()
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Cannot touch model '.$this->modelName.', it was not yet created.');

        if (!$this->hasTimestamps)
            throw new RuntimeException('Cannot touch model '.$this->modelName.', timestamps do not exist.');

        $updatedAtName = Table::UPDATED_AT;
        $this->$updatedAtName = Carbon::now()->toDateTimeString();

        return $this->query->touch();
    }

    public function delete()
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Cannot delete model, it was not yet created.');

        return $this->query->delete();
    }

    private function insert()
    {
        if ($this->isMissingRequiredColumn())
            throw new RuntimeException('A required column is missing from table ' . $this->tableName);

        $values = array();

        foreach ($this->vars as $key => $var)
        {
            if ($this->hasColumn($key))
                $values[$key] = $var;
        }

        $lastInsertId = $this->query->create($values);

        if ($lastInsertId == null || $lastInsertId <= 0)
            return false;

        $this->hydrate($this->find($lastInsertId));
        return true;
    }

    public static function create(array $values = null)
    {
        $instance = new static;

        if ($values != null)
            foreach ($values as $key => $value)
                $instance->$key = $value;

        if ($instance->insert())
            return $instance;

        return null;
    }

    protected function hydrate($otherModel)
    {
        if ($otherModel == null)
            return;

        foreach ($otherModel->vars as $key => $value)
            $this->$key = $value;
    }

    public static function exists($var, $value)
    {
        $instance = new static;
        $query = new Query($instance);

        if (!$instance->hasColumn($var))
            return false;

        return $query->exists($var, $value);
    }

    public static function all()
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->get();
    }

    public static function where($var, $operator, $value, $link = 'AND')
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->where($var, $operator, $value, $link);
    }

    public static function join($joinTableName, $on = null, $operator = '=', $to = null)
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->join($joinTableName, $on, $operator, $to);
    }

    public static function find($id)
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->where($instance->primaryKeyName, '=', $id)->get()->first();
    }

    public function hasColumn($name)
    {
        if ($name == $this->primaryKeyName)
            return true;

        foreach ($this->columnNames as $columnName)
        {
            if ($columnName === $name)
                return true;
        }

        return false;
    }

    public function isMissingRequiredColumn()
    {
        foreach ($this->columns as $column)
        {
            if ($column->isPrimaryKey())
                continue;

            if ($column->isRequired() && !isset($this->vars[$column->getName()]))
                return true;   
        }

        return false;
    }

    public function isMissingPrimaryKey()
    {
        return !isset($this->vars[$this->primaryKeyName]);
    }

    protected function camelCaseToUnderscore($str)
    {
        return strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $str));
    }

    /* RELATIONSHIPS */

    public function hasOne($modelName, $foreignKey = null)
    {
        if ($foreignKey == null)
            $foreignKey = $this->camelCaseToUnderscore($modelName) . '_id';

        if (!isset($this->vars[$foreignKey]))
            throw new RuntimeException('Relationship foreign key '.$foreignKey.' not found in '.$this->modelName.'.');

        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        $modelName = $this->modelDirectory . $modelName;
        return $modelName::find($this->vars[$foreignKey]);
    }

    public function belongsTo($modelName, $foreignKey = null)
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        if ($foreignKey == null)
            $foreignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        $modelName = $this->modelDirectory . $modelName;
        return $modelName::where($foreignKey, '=', $this->vars[$this->primaryKeyName])->get()->first();
    }

    public function hasMany($modelName, $foreignKey = null)
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        if ($foreignKey == null)
            $foreignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        $modelName = $this->modelDirectory . $modelName;
        return $modelName::where($foreignKey, '=', $this->vars[$this->primaryKeyName])->get();
    }

    public function hasManyThrough($modelName, $throughModelName, $foreignKey = null, $throughForeignKey = null)
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        if ($foreignKey == null)
            $foreignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        $throughModelName = $this->modelDirectory . $throughModelName;
        $throughModel = $throughModelName::where($foreignKey, '=', $this->vars[$this->primaryKeyName])->get()->first();

        return $throughModel->hasMany(ucfirst($modelName), $throughForeignKey);
    }

    public function belongsToMany($modelName, $pivotName = null, $thisForeignKey = null, $targetForeignKey = null)
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        if ($thisForeignKey == null)
            $thisForeignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        if ($targetForeignKey == null)
            $targetForeignKey = $this->camelCaseToUnderscore($modelName) . '_id';

        if ($pivotName == null)
        {
            if (strcmp($this->modelName, $modelName) < 0)
                $pivotName = $this->camelCaseToUnderscore($this->modelName) . '_' . $this->camelCaseToUnderscore($modelName);
            else if (strcmp($this->modelName, $modelName) > 0)
                $pivotName = $this->camelCaseToUnderscore($modelName) . '_' . $this->camelCaseToUnderscore($this->modelName);
            else
                throw new RuntimeException($this->modelName . ' cannot belong to the same table');
        }

        $targetIds = DB::query('SELECT '.$targetForeignKey.' FROM '.$pivotName.' WHERE '.
            $thisForeignKey.'='.$this->vars[$this->primaryKeyName])->fetchAll(\PDO::FETCH_COLUMN, 0);

        if (sizeof($targetIds) == 0)
            return new ModelCollection();

        $targetIds = '(' . implode(', ', $targetIds) . ')';

        $modelName = $this->modelDirectory.ucfirst($modelName);
        $model = new $modelName();
        $results = $modelName::where($model->primaryKeyName(), 'in', $targetIds)->get();
        return $results;
    }
}
