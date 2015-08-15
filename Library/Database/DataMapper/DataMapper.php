<?php

namespace Library\Database\DataMapper;

use Library\Database\Drivers\IDatabaseDriver;

class DataMapper
{
    protected $driver;
    protected $commands = [];

    public function __construct(IDatabaseDriver $driver)
    {
        $this->driver = $driver;
    }

    public function persist($object)
    {
        $this->addCommand('persist', $object);
    }

    public function flush()
    {
        foreach ($this->commands as $name => $data)
        {
            if (is_null($data))
            {
                $this->driver->$name();
                continue;
            }

            $this->driver->$name($data);
        }
    }

    protected function addCommand($name, $data = null)
    {
        $this->commands[$name] = $data;
    }
}