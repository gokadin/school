<?php

namespace Library\Container;

use ReflectionClass;

class Container
{
    protected $instances = [];
    protected $registeredInterfaces = [];

    protected $resolvedConcretes = [];
    protected $resolvedInterfaces = [];

    public function registerInstance($name, $instance)
    {
        $this->instances[$name] = $instance;
    }

    public function registerInterface($interface, $concrete)
    {
        if (is_object($concrete))
        {
            $this->registeredInterfaces[$interface] = get_class($concrete);
            $this->resolvedInterfaces[$interface] = $concrete;
            return;
        }

        $this->registeredInterfaces[$interface] = $concrete;
    }

    public function resolveInstance($name)
    {
        if (isset($this->instances[$name]))
        {
            return $this->instances[$name];
        }

        throw new ContainerException('Could not resolve '.$name.'.');
    }

    public function resolve($interface, $concrete = null)
    {
        if (!is_null($concrete))
        {
            return $this->resolveInterface($interface, $concrete);
        }

        return $this->resolveConcrete($interface);
    }

    protected function resolveInterface($interface)
    {
        if (isset($this->resolvedInterfaces[$interface]))
        {
            return $this->resolvedInterfaces[$interface];
        }

        if (!isset($this->registeredInterfaces[$interface]))
        {
            return null;
        }

        $concrete = $this->registeredInterfaces[$interface];

        return $this->resolvedInterfaces[$interface] = $this->resolveClassRecursive($concrete);
    }

    protected function resolveConcrete($concrete)
    {
        if (is_object($concrete))
        {
            return $concrete;
        }

        if ($this->resolvedConcretes[$concrete])
        {
            return $this->resolvedConcretes[$concrete];
        }

        return $this->resolvedConcretes[$concrete] = $this->resolveClassRecursive($concrete);
    }

    protected function resolveClassRecursive($class)
    {
        $r = new ReflectionClass($class);

        if (!$r->isInstantiable())
        {
            throw new ContainerException('Class '.$class.' cannot be instantiated.');
            return null;
        }

        if ($r->isInterface())
        {
            return $this->resolveInterface($class);
        }

        $constructor = $r->getConstructor();

        if (is_null($constructor))
        {
            return $r->newInstanceWithoutConstructor();
        }

        $parameters = $constructor->getParameters();
        $resolvedParameters = array();

        foreach ($parameters as $parameter)
        {
            if (is_null($parameter->getClass()))
            {
                if (!$parameter->isOptional())
                {
                    throw new ContainerException('Parameter '.$parameter->getName().' of class '.$class.' cannot be resolved.');
                    return null;
                }
                else
                {
                    $resolvedParameters[] = $parameter->getDefaultValue();
                }
            }

            $resolvedParameters[] = $this->resolveClassRecursive($parameter->getClass()->getName());
        }

        return $r->newInstanceArgs($resolvedParameters);
    }
}