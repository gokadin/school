<?php namespace Library\Database;

class Column
{
    protected $name;
    protected $isPrimaryKey;
    protected $type;
    protected $size;
    protected $precision;
    protected $canBeNull;
    protected $isUnique;
    protected $default;

    public function __construct($name, $type, $size = -1)
    {
        $this->name = $name;
        $this->type = $type;
        $this->canBeNull = false;
        $this->size = $size;
        $this->precision = 2;
        $this->isPrimaryKey = false;
        $this->isUnique = false;
        $this->default = null;
    }

    public function primaryKey()
    {
        $this->isPrimaryKey = true;
        return $this;
    }

    public function nullable()
    {
        $this->canBeNull = true;
        return $this;
    }

    public function unique()
    {
        $this->isUnique = true;
        return $this;
    }

    public function _default($value)
    {
        $this->default = $value;
        return $this;
    }

    public function precision($precision)
    {
        $this->precision = $precision;
        return $this;
    }

    public function __call($name, $args)
    {
        if ($name === 'default' && sizeof($args) == 1)
            return $this->_default($args[0]);
    }

    /* Accessor functions */

    public function getName()
    {
        return $this->name;
    }

    public function isPrimaryKey()
    {
        return $this->isPrimaryKey;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getSize()
    {
        return $this->size;
    }

    public function isNullable()
    {
        return $this->canBeNull;
    }

    public function isRequired()
    {
        if ($this->name === QueryBuilder::UPDATED_AT ||
            $this->name === QueryBuilder::CREATED_AT)
            return false;

        if ($this->name === Table::META_TYPE ||
            $this->name === Table::META_ID)
            return false;

        return !$this->isNullable() && !$this->isDefault();
    }

    public function isUnique()
    {
        return $this->isUnique;
    }

    public function isDefault()
    {
        return $this->default != null;
    }

    public function getDefault()
    {
        return $this->default;
    }
}