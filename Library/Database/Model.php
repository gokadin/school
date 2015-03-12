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
    protected $primaryKey;
    private $columns;
    protected $columnNames;
    private $hasTimestamps;
    private $isMeta;

    public function __construct(array $data = array())
    {
        $this->modelName = get_called_class();
        $this->modelName = substr($this->modelName, strrpos($this->modelName, '\\') + 1);
        $table = DB::getTable($this->modelName);
        $this->tableName = $table->tableName();
        $this->hasTimestamps = $table->hasTimestamps();
        $this->isMeta = $table->isMeta();
        foreach ($table->columns() as $column) {
            if ($column->isPrimaryKey())
                $this->primaryKey = $column->getName();
            else {
                $this->columns[] = $column;
                $this->columnNames[] = $column->getName();
            }
        }

        foreach ($data as $key => $value) {
            $this->__set($key, $value);
        }

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

    public function columnNames()
    {
        return $this->columnNames;
    }

    public function primaryKey()
    {
        return $this->primaryKey;
    }

    public function hasTimestamps()
    {
        return $this->hasTimestamps;
    }

    public function isMeta()
    {
        return $this->isMeta;
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

    public function save()
    {
        if ($this->isMissingPrimaryKey())
            return $this->insert();
        else
            return $this->update();
    }

    private function update()
    {
        if ($this->isMissingRequiredColumn()) {
            throw new RuntimeException('A required column is missing from table ' . $this->table);
            return false;
        }

        $values = array();

        foreach ($this->vars as $key => $var) {
            if ($this->hasColumn($key))
                $values[$key] = $var;
        }

        return $this->query->update($values) > 0;
    }

    public function touch()
    {
        if ($this->isMissingPrimaryKey()) {
            throw new RuntimeException('Cannot delete model, it was not yet created.');
            return false;
        }

        if (!$this->hasTimestamps) {
            throw new RuntimeException('Cannot perform touch. Timestamps are disabled or not present.');
            return false;
        }

        $this->vars[QueryBuilder::UPDATED_AT] = Carbon::now();

        return $this->update() > 0;
    }

    public function delete()
    {
        if ($this->isMissingPrimaryKey()) {
            throw new RuntimeException('Cannot delete model, it was not yet created.');
            return false;
        }

        return $this->query->delete() > 0;
    }

    private function insert()
    {
        if ($this->isMissingRequiredColumn()) {
            throw new RuntimeException('A required column is missing from table ' . $this->table);
            return false;
        }

        $values = array();

        foreach ($this->vars as $key => $var) {
            if ($this->hasColumn($key))
                $values[$key] = $var;
        }

        $result = $this->query->create($values);

        if ($result != null) {
            $this->hydrate($result->first());
            return true;
        }

        return false;
    }

    public static function create(array $values = null)
    {
        $instance = new static;

        if ($values != null) {
            foreach ($values as $key => $value) {
                if (!$instance->hasColumn($key)) {
                    throw new RuntimeException('Column ' . $key . ' does not exist in table ' . $instance->tableName());
                    return null;
                }

                $instance->$key = $value;
            }
        }

        if ($instance->isMissingRequiredColumn()) {
            throw new RuntimeException('A required column is missing from table ' . $instance->tableName());
            return null;
        }

        $query = new Query($instance);
        $result = $query->create($values);
        if ($result != null)
            return $result->first();

        return null;
    }

    protected function hydrate($otherModel)
    {
        foreach ($otherModel->vars as $key => $value)
            $this->$key = $value;
    }

    public static function exists($var, $value)
    {
        $instance = new static;
        $query = new Query($instance);
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

    public static function find($id)
    {
        $instance = new static;
        $query = new Query($instance);
        return $query->where($instance->primaryKey, '=', $id)->get()->first();
    }

    public function hasColumn($name)
    {
        foreach ($this->columnNames as $columnName) {
            if ($columnName === $name)
                return true;
        }

        return false;
    }

    public function isMissingRequiredColumn()
    {
        foreach ($this->columns as $column) {
            if ($column->isRequired() && !isset($this->vars[$column->getName()]))
                return true;
        }

        return false;
    }

    public function isMissingPrimaryKey()
    {
        return !isset($this->vars[$this->primaryKey]);
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
            throw new RuntimeException('Relationship foreign key not found.');

        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        $modelName = Query::MODEL_DIRECTORY . $modelName;
        return $modelName::find($this->vars[$foreignKey]);
    }

    public function belongsTo($modelName, $foreignKey = null)
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        if ($foreignKey == null)
            $foreignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        $modelName = Query::MODEL_DIRECTORY . $modelName;
        return $modelName::where($foreignKey, '=', $this->vars[$this->primaryKey])->get()->first();
    }

    public function hasMany($modelName, $foreignKey = null)
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

        if ($foreignKey == null)
            $foreignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        $modelName = Query::MODEL_DIRECTORY . $modelName;
        return $modelName::where($foreignKey, '=', $this->vars[$this->primaryKey])->get();
    }

    public function hasManyThrough($modelName, $throughModelName, $foreignKey = null, $throughForeignKey = null)
    {
        if ($this->isMissingPrimaryKey()) {
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');
            return null;
        }

        if ($foreignKey == null)
            $foreignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        if ($throughForeignKey == null)
            $throughForeignKey = $this->camelCaseToUnderscore($throughModelName);

        $modelName = Query::MODEL_DIRECTORY . $modelName;
        $throughModelName = Query::MODEL_DIRECTORY . $throughModelName;

        $throughModels = $throughModelName::where($foreignKey, '=', $this->vars[$this->primaryKey])->get();

        $throughModelNameIds = array();
        foreach ($throughModels as $throughModel)
            $throughModelNameIds[] = $throughModel->id;
        $throughModelNameIds = '(' . implode(', ', $throughModelNameIds) . ')';

        return $modelName::where($throughForeignKey, 'in', $throughModelNameIds)->get();
    }

    public function belongsToMany($modelName, $pivotName = null, $thisForeignKey = null, $targetForeignKey = null)
    {
        if ($this->isMissingPrimaryKey()) {
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');
            return null;
        }

        if ($thisForeignKey == null)
            $thisForeignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        if (!isset($this->vars[$thisForeignKey])) {
            throw new RuntimeException('Relationship foreign key not found.');
            return null;
        }

        if ($targetForeignKey == null)
            $targetForeignKey = $this->camelCaseToUnderscore($modelName) . '_id';

        if ($pivotName == null) {
            if (strcmp($this->modelName, $modelName) < 0)
                $pivotName = $this->camelCaseToUnderscore($this->modelName) . '_' . $this->camelCaseToUnderscore($modelName);
            else if (strcmp($this->modelName, $modelName) > 0)
                $pivotName = $this->camelCaseToUnderscore($modelName) . '_' . $this->camelCaseToUnderscore($this->modelName);
            else {
                throw new RuntimeException($this->modelName . ' cannot belong to the same table');
                return null;
            }
        }

        $targetIds = $pivotName::where($thisForeignKey, '=', $this->vars[$this->primaryKey])->get($targetForeignKey);
        $targetIds = '(' . implode(', ', $targetIds) . ')';

        $model = new $modelName();
        return $modelName::where($model->primaryKey(), 'in', $targetIds)->get()->first();
    }

    public function morphTo($metaIdField = Table::META_ID, $metaTypeField = Table::META_TYPE, $thisForeignKey = null)
    {
        if (!$this->isMeta)
            throw new RuntimeException('Model is not a meta type.');

        if (!isset($this->vars[$metaTypeField]) || !isset($this->vars[$metaIdField]))
            throw new RuntimeException('Model meta field names are invalid or do not exist.');

        $metaType = $this->$metaTypeField;

        if ($metaType == null || !is_string($metaType))
            throw new RuntimeException('Meta type is invalid or is not defined.');

        if (!class_exists($metaType))
            throw new RuntimeException('Model '.$metaType.' does not exist,');

        $metaModel = new $metaType();

        if ($thisForeignKey == null)
            $thisForeignKey = $this->camelCaseToUnderscore($this->modelName) . '_id';

        return $metaModel::where($thisForeignKey, '=', $this->$metaIdField)->get()->first();
    }

    public function morphOne($modelName, $metaIdField = Table::META_ID, $metaTypeField = Table::META_TYPE, $typeName = null)
    {
        $result = $this->morphMany($modelName, $metaIdField, $metaTypeField, $typeName);

        if ($result == null)
            return null;

        return $result->first();
    }

    public function morphMany($modelName, $metaIdField = Table::META_ID, $metaTypeField = Table::META_TYPE, $typeName = null)
    {
        $modelName = Query::MODEL_DIRECTORY . $modelName;
        $model = new $modelName();

        return $model::where($metaTypeField, '=', $this->modelName)
            ->where($metaIdField, '=', $this->vars[$this->primaryKey])
            ->get();
    }
}
