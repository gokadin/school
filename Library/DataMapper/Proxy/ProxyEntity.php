<?php

namespace Library\DataMapper\Proxy;

use Library\DataMapper\Mapping\Association;
use Library\DataMapper\UnitOfWork\UnitOfWork;

class ProxyEntity
{
    /**
     * @var UnitOfWork
     */
    private $uow;

    /**
     * @var Association
     */
    private $association;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $parentClass;

    /**
     * @var mixed
     */
    private $parentId;

    public function __construct(UnitOfWork $uow, Association $association, int $id,
                                string $parentClass, $parentId)
    {
        $this->uow = $uow;
        $this->association = $association;
        $this->id = $id;
        $this->parentClass = $parentClass;
        $this->parentId = $parentId;
    }

    public function __get($name)
    {
        $entity = $this->resolve();

        return $entity->$name;
    }

    public function __call($name, $arguments)
    {
        $entity = $this->resolve();

        return $this->callMethod($entity, $name, $arguments);
    }

    private function resolve()
    {
        $entity = $this->uow->find($this->association->target(), $this->id);

        $this->uow->replaceProxy($this->parentClass, $this->parentId, $this->association->propName(), $entity);

        return $entity;
    }

    private function callMethod($entity, $name, array $arguments)
    {
        switch (sizeof($arguments))
        {
            case 0:
                return $entity->$name();
            case 1:
                return $entity->$name($arguments[0]);
            case 2:
                return $entity->$name($arguments[0], $arguments[1]);
            case 3:
                return $entity->$name($arguments[0], $arguments[1], $arguments[2]);
            case 4:
                return $entity->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3]);
            case 5:
                return $entity->$name($arguments[0], $arguments[1], $arguments[2], $arguments[3], $arguments[4]);
            default:
                return call_user_func_array([$entity, $name], $arguments);
        }
    }
}