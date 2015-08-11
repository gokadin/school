<?php

namespace Tests\FrameworkTest\Queue;

use Library\Queue\Drivers\SyncQueueDriver;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Container\ConcreteNoConstructor;
use Tests\FrameworkTest\TestData\Queue\SimpleEventListener;
use Tests\FrameworkTest\TestData\Queue\ResolvableJob;
use Tests\FrameworkTest\TestData\Queue\SimpleEvent;
use Tests\FrameworkTest\TestData\Queue\SimpleJob;

class SyncQueueDriverTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        $this->createApplication();
    }

    public function testPushWithSimpleJob()
    {
        // Arrange
        $driver = new SyncQueueDriver();
        $job = new SimpleJob();

        // Act
        $driver->push($job);

        // Assert
        $this->assertTrue($job->wasRun());
    }

    public function testPushWithResolvableJob()
    {
        // Arrange
        $driver = new SyncQueueDriver();
        $job = new ResolvableJob();

        // Act
        $driver->push($job);

        // Assert
        $this->assertTrue($job->wasRun());
        $this->assertTrue($job->handleDependency() instanceof ConcreteNoConstructor);
    }

    public function testPushWithSimpleEvent()
    {
        // Arrange
        $driver = new SyncQueueDriver();
        $event = new SimpleEvent(3);
        $listener = new SimpleEventListener();

        // Act
        $driver->push($event, $listener);

        // Assert
        $this->assertTrue($listener->wasRun());
        $this->assertEquals(3, $listener->eventValue());
    }
}