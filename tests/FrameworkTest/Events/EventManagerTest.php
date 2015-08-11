<?php

namespace Tests\FrameworkTest\Events;

use Library\Events\EventManager;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Events\EventTestingResolvableConstructor;
use Tests\FrameworkTest\TestData\Events\SimpleEvent;
use Tests\FrameworkTest\TestData\Events\SimpleEventListener;
use Tests\FrameworkTest\TestData\Events\SimpleEventListenerTwo;
use Tests\FrameworkTest\TestData\Events\ListenerWithResolvableConstructor;

class EventManagerTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        $this->createApplication();
    }

    public function testRegisterEvent()
    {
        // Arrange
        $eventManager = new EventManager();

        // Act
        $eventManager->register('x', ['x1', 'x2']);
        $xListeners = $eventManager->getListeners('x');
        $eventManager->register('y', ['y1', 'y2']);
        $yListeners = $eventManager->getListeners('y');

        // Assert
        $this->assertEquals(2, sizeof($xListeners));
        $this->assertTrue(in_array('x1', $xListeners));
        $this->assertTrue(in_array('x2', $xListeners));
        $this->assertEquals(2, sizeof($yListeners));
        $this->assertTrue(in_array('y1', $yListeners));
        $this->assertTrue(in_array('y2', $yListeners));
    }

    public function testFireWithSimpleEvent()
    {
        // Arrange
        $eventManager = new EventManager();
        $eventManager->register(SimpleEvent::class, [
            SimpleEventListener::class
        ]);

        // Act
        $event = new SimpleEvent();
        $eventManager->fire($event);

        // Assert
        $this->assertTrue($event->hasFired());
    }

    public function testFireWithSimpleEventAndMultipleListeners()
    {
        // Arrange
        $eventManager = new EventManager();
        $eventManager->register(SimpleEvent::class, [
            SimpleEventListener::class,
            SimpleEventListenerTwo::class
        ]);

        // Act
        $event = new SimpleEvent();
        $eventManager->fire($event);

        // Assert
        $this->assertTrue($event->hasFired());
        $this->assertTrue($event->secondHasFired());
    }

    public function testListenerConstructorCanBeResolved()
    {
        // Arrange
        $eventManager = new EventManager();
        $eventManager->register(EventTestingResolvableConstructor::class, [
            ListenerWithResolvableConstructor::class
        ]);

        // Act
        $event = new EventTestingResolvableConstructor();
        $eventManager->fire($event);

        // Assert
        $this->assertTrue($event->hasFired());
        $this->assertTrue($event->resolvedParameter() instanceof SimpleEvent);
    }
}