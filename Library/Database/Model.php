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
    protected $inheritsFrom = null;
    private $baseModel = null;

    public function __construct(array $data = array())
    {
        $this->modelName = get_called_class();
        $this->modelName = substr($this->modelName, strrpos($this->modelName, '\\') + 1);
        $table = DB::getTable($this->modelName);
        $this->tableName = $table->tableName();
        $this->hasTimestamps = $table->hasTimestamps();
        $this->isMeta = $table->isMeta();
        foreach ($table->columns() as $column)
        {
            if ($column->isPrimaryKey())
                $this->primaryKey = $column->getName();
            else {
                $this->columns[] = $column;
                $this->columnNames[] = $column->getName();
            }
        }

        if ($this->inheritsFrom != null)
        {
            $baseModelName = Query::MODEL_DIRECTORY.ucfirst($this->inheritsFrom);
            $this->baseModel = new $baseModelName();
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

    public function hasBaseModel()
    {
        return $this->baseModel != null;
    }

    public function baseModel()
    {
        return $this->baseModel;
    }

    public function __set($var, $value)
    {
        if (!$this->hasColumn($var) && $this->hasBaseModel())
        {
            if ($this->baseModel->hasColumn($var))
            {
                $this->baseModel->$var = $value;
                return;
            }
        }

        $this->vars[$var] = $value;
    }

    public function __get($var)
    {
        if (isset($this->vars[$var]))
            return $this->vars[$var];
        else if ($this->hasBaseModel() && $var != $this->primaryKey)
        {
            return $this->baseModel->$var;
        }
    }

    public function __isset($var)
    {
        if (isset($this->vars[$var]))
            return true;

        if ($this->hasBaseModel())
            return isset($this->baseModel->$var);
    }

    public function __call($method, $args)
    {
        if (!$this->hasBaseModel())
            throw new RuntimeException('Method not found: '.$method);

        switch (count($args))
        {
            case 0:
                return $this->baseModel->$method();
            case 1:
                return $this->baseModel->$method($args[0]);
            case 2:
                return $this->baseModel->$method($args[0], $args[1]);
            case 3:
                return $this->baseModel->$method($args[0], $args[1], $args[2]);
            case 4:
                return $this->baseModel->$method($args[0], $args[1], $args[2], $args[3]);
            default:
                return call_user_func_array(array($this->baseModel, $method), $args);
        }
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
            if ($this->hasColumn($key))
                $values[$key] = $var;
        }

        if ($this->hasBaseModel())
            return $this->query->update($values) > 0 && $this->baseModel->update();

        return $this->query->update($values) > 0;
    }

    public function touch()
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Cannot touch model '.$this->modelName.', it was not yet created.');

        if ($this->hasTimestamps)
        {
            $this->vars[QueryBuilder::UPDATED_AT] = Carbon::now();
            if ($this->update() == 0)
                return false;
        }

        if ($this->hasBaseModel() && $this->baseModel->hasTimestamps())
            return $this->baseModel->touch();

        return true;
    }

    public function delete()
    {
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Cannot delete model, it was not yet created.');

        if ($this->hasBaseModel())
            return $this->query->delete() > 0 && $this->baseModel->delete();

        return $this->query->delete() > 0;
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

        $result = $this->query->create($values);

        if ($result != null)
        {
            $this->hydrate($result->first());

            if ($this->hasBaseModel())
            {
                $metaTypeField = Table::META_TYPE;
                $metaIdField = Table::META_ID;
                $this->baseModel->$metaTypeField = ucfirst($this->modelName);
                $this->baseModel->$metaIdField = $this->vars[$this->primaryKey];

                return $this->baseModel->save();
            }

            return true;
        }

        return false;
    }

    public static function create(array $values = null)
    {
        $instance = new static;

        if ($values != null)
        {
            foreach ($values as $key => $value)
                $instance->$key = $value;
        }

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

    public function hydrateBaseModel()
    {
        if (!$this->hasBaseModel())
            return;

        $this->baseModel->hydrate($this->morphOne($this->baseModel->modelName()));
    }

    public static function exists($var, $value)
    {
        $instance = new static;
        $query = new Query($instance);

        if (!$instance->hasColumn($var))
        {
            if ($instance->hasBaseModel())
            {
                $baseModelName = Query::MODEL_DIRECTORY.$instance->baseModel()->modelName();
                return $baseModelName::exists($var, $value);
            }
            else
                throw new RuntimeException('Field '.$var.' does not exists in model '.$instance->modelName());
        }

        return $query->exists($var, $value);
    }

    public static function all()
    {
        $instance = new static;
        $query = new Query($instance);
        $all = $query->get();
        return $all;
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
        $model = $query->where($instance->primaryKey, '=', $id)->get()->first();

        if ($model == null)
            return null;

        $model->hydrateBaseModel();

        return $model;
    }

    public function hasColumn($name)
    {
        if ($name == $this->primaryKey)
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
            throw new RuntimeException('Relationship foreign key '.$foreignKey.' not found in '.$this->modelName.'.');

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
        if ($this->isMissingPrimaryKey())
            throw new RuntimeException('Primary key not found in table ' . $this->tableName . '.');

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

        $targetIds = DB::query('SELECT '.$thisForeignKey.' FROM '.$pivotName.' WHERE '.
            $thisForeignKey.'='.$this->vars[$this->primaryKey])->fetchAll(\PDO::FETCH_COLUMN, 0);

        if (sizeof($targetIds) == 0)
            return new ModelCollection();

        $targetIds = '(' . implode(', ', $targetIds) . ')';

        $model = new $modelName();
        return $modelName::where($model->primaryKey(), 'in', $targetIds)->get()->first();
    }

    public function morphTo($metaIdField = Table::META_ID, $metaTypeField = Table::META_TYPE)
    {
        if (!$this->isMeta)
            throw new RuntimeException('Model is not a meta type.');

        if (!isset($this->vars[$metaTypeField]) || !isset($this->vars[$metaIdField]))
            throw new RuntimeException('Model meta field names are invalid or do not exist.');

        $metaType = ucfirst($this->$metaTypeField);

        if ($metaType == null || !is_string($metaType))
            throw new RuntimeException('Meta type is invalid or is not defined.');

        $metaType = Query::MODEL_DIRECTORY.$metaType;
        if (!class_exists($metaType))
            throw new RuntimeException('Model '.$metaType.' does not exist,');

        $metaModel = new $metaType();
        $metaModel = $metaType::where($metaModel->primaryKey(), '=', $this->$metaIdField)->get()->first();
        $metaModel->hydrateBaseModel();
        return $metaModel;
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
        $modelName = Query::MODEL_DIRECTORY . ucfirst($modelName);
        $model = new $modelName();

        if ($typeName == null)
            $typeName = $this->modelName;

        return $model::where($metaTypeField, '=', $typeName)
            ->where($metaIdField, '=', $this->vars[$this->primaryKey])
            ->get();
    }
}
