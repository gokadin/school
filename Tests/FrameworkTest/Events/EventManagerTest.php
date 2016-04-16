<?php

namespace Tests\FrameworkTest\Events;

use Library\Container\Container;
use Library\Events\EventManager;
use Library\Queue\Queue;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Events\EventTestingResolvableConstructor;
use Tests\FrameworkTest\TestData\Events\SimpleEvent;
use Tests\FrameworkTest\TestData\Events\SimpleEventListener;
use Tests\FrameworkTest\TestData\Events\SimpleEventListenerTwo;
use Tests\FrameworkTest\TestData\Events\ListenerWithResolvableConstructor;

class EventManagerTest extends BaseTest
{
    /**
     * @var EventManager
     */
    private $eventManager;

    public function setUp()
    {
        parent::setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    private function setUpEventManager(array $config)
    {
        $queueConfig = ['use' => 'sync'];

        $this->eventManager = new EventManager($config, new Container(), new Queue($queueConfig));
    }

    public function testRegisterEvent()
    {
        // Arrange
        $this->setUpEventManager(['ev1' => ['x1', 'x2'], 'ev2' => ['y1']]);
        $xListeners = $this->eventManager->getListeners('ev1');
        $yListeners = $this->eventManager->getListeners('ev2');

        // Assert
        $this->assertEquals(2, sizeof($xListeners));
        $this->assertTrue(in_array('x1', $xListeners));
        $this->assertTrue(in_array('x2', $xListeners));
        $this->assertEquals(1, sizeof($yListeners));
        $this->assertTrue(in_array('y1', $yListeners));
    }

    public function testFireWithSimpleEvent()
    {
        // Arrange
        $this->setUpEventManager([
            SimpleEvent::class => [SimpleEventListener::class]
        ]);

        // Act
        $event = new SimpleEvent();
        $this->eventManager->fire($event);

        // Assert
        $this->assertTrue($event->hasFired());
    }

    public function testFireWithSimpleEventAndMultipleListeners()
    {
        // Arrange
        $this->setUpEventManager([
            SimpleEvent::class => [SimpleEventListener::class, SimpleEventListenerTwo::class]
        ]);

        // Act
        $event = new SimpleEvent();
        $this->eventManager->fire($event);

        // Assert
        $this->assertTrue($event->hasFired());
        $this->assertTrue($event->secondHasFired());
    }

    public function testListenerConstructorCanBeResolved()
    {
        // Arrange
        $this->setUpEventManager([
            EventTestingResolvableConstructor::class => [ListenerWithResolvableConstructor::class]
        ]);

        // Act
        $event = new EventTestingResolvableConstructor();
        $this->eventManager->fire($event);

        // Assert
        $this->assertTrue($event->hasFired());
        $this->assertTrue($event->resolvedParameter() instanceof SimpleEvent);
    }
}