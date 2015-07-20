<?php

namespace Library\Database;

use Faker\Factory as FakerFactory;

class Factory
{
    const MODEL_FACTORY_DIR = 'Database/Factories';

    protected $faker;
    protected $definitions;

    public function __construct()
    {
        $this->faker = FakerFactory::create();
        $this->definitions = array();

        $factory = $this;
        require __DIR__.'/../../'.self::MODEL_FACTORY_DIR.'/modelFactory.php';
    }

    public function define($class, callable $attributes)
    {
        $this->definitions[$class] = $attributes;
    }

    public function of($class)
    {
        return new FactoryBuilder($class, $this->definitions, $this->faker);
    }
}