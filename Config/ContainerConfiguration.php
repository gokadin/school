<?php

namespace Config;

use App\Repositories\Contracts\IUserRepository;
use App\Repositories\UserRepository;

class ContainerConfiguration
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function configureContainer()
    {
        $this->container->registerInterface(IUserRepository::class, UserRepository::class);
    }
}