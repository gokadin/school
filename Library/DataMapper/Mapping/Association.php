<?php

namespace Library\DataMapper\Mapping;

class Association
{
    /**
     * @var Column
     */
    private $column;

    private $type;

    private $target;

    /**
     * @var array
     */
    private $cascades = [];

    private $mappedBy;

    public function __construct($column, $type, $target, array $cascades, $mappedBy = null)
    {
        $this->column = $column;
        $this->type = $type;
        $this->target = $target;
        $this->cascades = $cascades;
        $this->mappedBy = $mappedBy;
    }

    public function column()
    {
        return $this->column;
    }

    public function type()
    {
        return $this->type;
    }

    public function target()
    {
        return $this->target;
    }

    public function cascades()
    {
        return $this->cascades;
    }

    public function isNullable()
    {
        return $this->column->isNullable();
    }

    public function mappedBy()
    {
        return $this->mappedBy;
    }

    public function hasDeleteCascade()
    {
        foreach ($this->cascades as $cascade)
        {
            if ($cascade == Metadata::CASCADE_DELETE)
            {
                return true;
            }
        }

        return false;
    }

    public function hasTouchCascade()
    {
        foreach ($this->cascades as $cascade)
        {
            if ($cascade == Metadata::CASCADE_TOUCH)
            {
                return true;
            }
        }

        return false;
    }
}