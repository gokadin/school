<?php

namespace Tests\FrameworkTest\Container;

use Library\Container\Container;
use Tests\FrameworkTest\BaseTest;
use Tests\FrameworkTest\TestData\Container\ConcreteEmptyConstructor;
use Tests\FrameworkTest\TestData\Container\ConcreteImplementingInterfaceOne;
use Tests\FrameworkTest\TestData\Container\ConcreteImplementingInterfaceTwo;
use Tests\FrameworkTest\TestData\Container\ConcreteNoConstructor;
use Tests\FrameworkTest\TestData\Container\ConcreteNoTypeHintDefault;
use Tests\FrameworkTest\TestData\Container\ConcreteNoTypeHintNoDefault;
use Tests\FrameworkTest\TestData\Container\ConcreteWithDefaultInterface;
use Tests\FrameworkTest\TestData\Container\ConcreteWithMixedButValid;
use Tests\FrameworkTest\TestData\Container\ConcreteWithMixedButValidIncludingInterfaces;
use Tests\FrameworkTest\TestData\Container\ConcreteWithMultipleComplexTypeHint;
use Tests\FrameworkTest\TestData\Container\ConcreteWithMultipleSimpleTypeHint;
use Tests\FrameworkTest\TestData\Container\ConcreteWithSimpleTypeHint;
use Tests\FrameworkTest\TestData\Container\InterfaceOne;

class ContainerTest extends BaseTest
{
    public function testResolveInstanceWhenInstanceIsRegistered()
    {
        // Arrange
        $container = new Container();
        $container->registerInstance('test', new ConcreteNoConstructor());
        $container->registerInstance('test2', new ConcreteEmptyConstructor());

        // Act
        $resolved = $container->resolveInstance('test');

        // Assert
        $this->assertTrue($resolved instanceof ConcreteNoConstructor);
    }

    /**
     * @expectedException \Library\Container\ContainerException
     */
    public function testResolveInstanceWhenInstanceIsNotRegistered()
    {
        // Arrange
        $container = new Container();
        $container->registerInstance('test2', new ConcreteNoConstructor());

        // Act
        $resolved = $container->resolveInstance('test');

        // Assert
        $this->assertNull($resolved);
    }

    public function testResolveForConcreteWithoutConstructor()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteNoConstructor::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteNoConstructor);
    }

    public function testResolveForConcreteWithEmptyConstructor()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteEmptyConstructor::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteEmptyConstructor);
    }

    public function testResolveForConcreteWithNonTypeHintedDefaultParameter()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteNoTypeHintDefault::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteNoTypeHintDefault);
        $this->assertEquals(3, $resolved->getA());
    }

    /**
     * @expectedException \Library\Container\ContainerException
     */
    public function testResolveForConcreteWithNonTypeHintedNonDefaultParameter()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteNoTypeHintNoDefault::class);

        // Assert
        $this->assertNull($resolved);
    }

    public function testResolveForConcreteWithSimpleTypeHintedParameter()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteWithSimpleTypeHint::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteWithSimpleTypeHint);
        $this->assertTrue($resolved->getA() instanceof ConcreteNoConstructor);
    }

    public function testResolveForConcreteWithMultipleSimpleTypeHintedParameters()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteWithMultipleSimpleTypeHint::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteWithMultipleSimpleTypeHint);
        $this->assertTrue($resolved->getA() instanceof ConcreteNoConstructor);
        $this->assertTrue($resolved->getB() instanceof ConcreteNoConstructor);
        $this->assertTrue($resolved->getC() instanceof ConcreteNoConstructor);
    }

    public function testResolveForConcreteWithMultipleComplexTypeHintedParameters()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteWithMultipleComplexTypeHint::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteWithMultipleComplexTypeHint);
        $this->assertTrue($resolved->getA() instanceof ConcreteWithSimpleTypeHint);
        $this->assertTrue($resolved->getB() instanceof ConcreteWithMultipleSimpleTypeHint);
    }

    public function testResolveForConcreteWithMixedButValidParameters()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteWithMixedButValid::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteWithMixedButValid);
        $this->assertTrue($resolved->getA() instanceof ConcreteWithMultipleComplexTypeHint);
        $this->assertTrue($resolved->getB() instanceof ConcreteNoTypeHintDefault);
        $this->assertEquals(3, $resolved->getC());
    }

    public function testResolveForInterface()
    {
        // Arrange
        $container = new Container();
        $container->registerInterface(InterfaceOne::class, ConcreteImplementingInterfaceOne::class);

        // Act
        $resolved = $container->resolve(InterfaceOne::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteImplementingInterfaceOne);
    }

    public function testResolveForInterfaceWithMixedButValidParameters()
    {
        // Arrange
        $container = new Container();
        $container->registerInterface(InterfaceOne::class, ConcreteImplementingInterfaceOne::class);

        // Act
        $resolved = $container->resolve(ConcreteWithMixedButValidIncludingInterfaces::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteWithMixedButValidIncludingInterfaces);
        $this->assertTrue($resolved->getA() instanceof ConcreteImplementingInterfaceOne);
        $this->assertTrue($resolved->getB() instanceof ConcreteWithMultipleComplexTypeHint);
        $this->assertEquals(3, $resolved->getC());
    }

    /**
     * @expectedException \Library\Container\ContainerException
     */
    public function testResolveForInterfaceWhenNotRegistered()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(InterfaceOne::class);

        // Assert
        $this->assertNull($resolved);
    }

    public function testResolveForInterfaceWhenNotRegisteredButWithDefault()
    {
        // Arrange
        $container = new Container();

        // Act
        $resolved = $container->resolve(ConcreteWithDefaultInterface::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteWithDefaultInterface);
        $this->assertNull($resolved->getA());
    }

    public function testResolveForConcreteWhenAParameterIsRegisteredAsInstance()
    {
        // Arrange
        $container = new Container();
        $container->registerInstance('test', new ConcreteNoConstructor());

        // Act
        $resolved = $container->resolve(ConcreteWithSimpleTypeHint::class);

        // Assert
        $this->assertTrue($resolved instanceof ConcreteWithSimpleTypeHint);
        $this->assertTrue($resolved->getA() instanceof ConcreteNoConstructor);
    }
}