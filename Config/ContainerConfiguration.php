<?php

namespace Config;

class ContainerConfiguration
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function configureContainer()
    {
        // Example:
        //$this->container->registerInterface(IUserRepository::class, UserRepository::class);
    }
}