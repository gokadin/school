<?php

namespace Tests\FrameworkTest\DataMapper\Proxy;

use Library\DataMapper\Proxy\ProxyEntity;
use Tests\FrameworkTest\DataMapper\DataMapperBaseTest;
use Tests\FrameworkTest\TestData\DataMapper\LazyEntityOne;
use Tests\FrameworkTest\TestData\DataMapper\LazyEntityTwo;

class ProxyEntityTest extends DataMapperBaseTest
{
    public function testHasOneLazyLoadedPropertyIsProxied()
    {
        // Arrange
        $this->setUpLazyEntities();
        $one = new LazyEntityOne('one');
        $two = new LazyEntityTwo('two', $one);
        $one->setEntityTwo($two);
        $this->dm->persist($one);
        $this->dm->persist($two);
        $this->dm->flush();
        $this->dm->detachAll();

        // Act
        $one = $this->dm->find(LazyEntityOne::class, $one->getId());

        // Assert
        $this->assertNotNull($one);
        $this->assertTrue($one->entityTwo() instanceof ProxyEntity);
    }

    public function testBelongsToLazyLoadedPropertyIsProxied()
    {
        // Arrange
        $this->setUpLazyEntities();
        $one = new LazyEntityOne('one');
        $two = new LazyEntityTwo('two', $one);
        $one->setEntityTwo($two);
        $this->dm->persist($one);
        $this->dm->persist($two);
        $this->dm->flush();
        $this->dm->detachAll();

        // Act
        $one = $this->dm->find(LazyEntityTwo::class, $one->getId());

        // Assert
        $this->assertNotNull($one);
        $this->assertTrue($one->entityOne() instanceof ProxyEntity);
    }

    /**
     * @depends testHasOneLazyLoadedPropertyIsProxied
     */
    public function testProxyIsResolvedWheneverAMethodIsCalled()
    {
        // Arrange
        $this->setUpLazyEntities();
        $one = new LazyEntityOne('one');
        $two = new LazyEntityTwo('two', $one);
        $one->setEntityTwo($two);
        $this->dm->persist($one);
        $this->dm->persist($two);
        $this->dm->flush();
        $this->dm->detachAll();

        // Act
        $one = $this->dm->find(LazyEntityOne::class, $one->getId());
        $name = $one->entityTwo()->name();

        // Assert
        $this->assertTrue($one->entityTwo() instanceof LazyEntityTwo);
        $this->assertEquals($two->name(), $name);
    }
}