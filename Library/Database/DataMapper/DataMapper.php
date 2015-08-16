<?php

namespace Library\Database\DataMapper;

use Library\Database\Database;

class DataMapper
{
    protected $database;
    protected $commands = [];

    public function __construct(Database $database)
    {
        $this->database = $database;
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
                $this->database->$name();
                continue;
            }

            $this->database->$name($data);
        }
    }

    protected function addCommand($name, $data = null)
    {
        $this->commands[$name] = $data;
    }
}