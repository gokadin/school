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

    public function resolve($class)
    {
        if (is_object($class))
        {
            return $class;
        }

       return $this->resolveParameter($class);
    }

    protected function resolveParameter($class, $interfaceDefault = null, $useInterfaceDefault = false)
    {
        $r = new ReflectionClass($class);

        if ($r->isInterface())
        {
            return $this->resolveInterface($class, $interfaceDefault, $useInterfaceDefault);
        }

        if (!$r->isInstantiable())
        {
            throw new ContainerException('Class '.$class.' cannot be instantiated.');
            return null;
        }

        return $this->resolveConcrete($class, $r);
    }

    protected function resolveInterface($interface, $default, $useDefault)
    {
        if (isset($this->resolvedInterfaces[$interface]))
        {
            return $this->resolvedInterfaces[$interface];
        }

        if (!isset($this->registeredInterfaces[$interface]))
        {
            if ($useDefault)
            {
                return $default;
            }

            throw new ContainerException('Interface '.$interface.' is not registered.');
            return null;
        }

        $concrete = $this->registeredInterfaces[$interface];

        $r = new ReflectionClass($concrete);

        return $this->resolvedInterfaces[$interface] = $this->resolveClassRecursive($concrete, $r);
    }

    protected function resolveConcrete($concrete, ReflectionClass $r)
    {
        if (isset($this->resolvedConcretes[$concrete]))
        {
            return $this->resolvedConcretes[$concrete];
        }

        return $this->resolvedConcretes[$concrete] = $this->resolveClassRecursive($concrete, $r);
    }

    protected function resolveClassRecursive($class, ReflectionClass $r)
    {
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
                    continue;
                }
            }

            $defaultValue = null;
            $useDefault = $parameter->isOptional();
            if ($useDefault)
            {
                $defaultValue = $parameter->getDefaultValue();
            }

            $resolvedParameters[] = $this->resolveParameter($parameter->getClass()->getName(), $defaultValue, $useDefault);
        }

        return $r->newInstanceArgs($resolvedParameters);
    }
}