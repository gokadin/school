<?php

namespace ApplicationTest\Domain\Services;

use App\Domain\Services\EventService;
use Tests\ApplicationTest\BaseTest;

class EventServiceTest extends BaseTest
{
    /**
     * @var EventService
     */
    private $service;

    public function setUp()
    {
        parent::setUp();

        $this->createApplication();

        $this->service = $this->app->container()->resolve(EventService::class);
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testUpdateDate()
    {
        // Arrange


        // Act

        // Assert
    }
}