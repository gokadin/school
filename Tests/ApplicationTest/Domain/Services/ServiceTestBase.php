<?php

namespace Tests\ApplicationTest\Domain\Services;

use App\Domain\Services\Service;
use Library\Container\Container;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Tests\ApplicationTest\BaseTest;

class ServiceTestBase extends BaseTest
{
    /**
     * @var Service
     */
    protected $service;

    /**
     * @var Container
     */
    private $container;

    public function setUp()
    {
        parent::setUp();

        $this->container = new Container();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    protected function setUpService(string $class, array $datamapperClasses)
    {
        $this->setUpDatamapper($datamapperClasses);

        $queue = new Queue(['use' => 'sync']);
        $this->container->registerInstance('queue', $queue);
        $this->container->registerInstance('eventManager', new EventManager([], $this->container, $queue));
        $this->container->registerInstance('datamapper', $this->dm);

        $this->service = $this->container->resolve($class);
    }
}