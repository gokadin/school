<?php

namespace Tests\FrameworkTest\DataMapper\Collection;

use Library\DataMapper\Collection\PersistentCollection;
use Tests\FrameworkTest\DataMapper\DataMapperBaseTest;
use Tests\FrameworkTest\TestData\DataMapper\SimpleEntity;
use Exception;

class PersistentCollectionTest extends DataMapperBaseTest
{
    public function testAddingItemsThroughTheConstructorDoesNotAffectTheStateOfTheCollection()
    {
        // Arrange
        $this->setUpSimpleEntity();

        // Act
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, [
            new SimpleEntity(1, 2, 'one', 'two'),
            new SimpleEntity(1, 2, 'one', 'two'),
            new SimpleEntity(1, 2, 'one', 'two'),
        ]);

        // Assert
        $this->assertEquals(3, $collection->count());
        $this->assertFalse($collection->isChanged());
        $this->assertEquals(0, $collection->addedItems()->count());
        $this->assertEquals(0, $collection->removedItems()->count());
    }

    public function testAddingItemsCorrectlyChangesState()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $collection = new PersistentCollection($this->dm, SimpleEntity::class);
        $e1 = new SimpleEntity(1, 2, 'one', 'two');
        $e2 = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $collection->add($e1);
        $collection->add($e2);

        // Assert
        $this->assertEquals(2, $collection->count());
        $this->assertTrue($collection->isChanged());
        $this->assertEquals(0, $collection->removedItems()->count());
        $this->assertEquals(2, $collection->addedItems()->count());
        $this->assertTrue($collection->addedItems()->contains($e1));
        $this->assertTrue($collection->addedItems()->contains($e2));
    }

    public function testRemovingItemsCorrectlyChangesState()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $collection = new PersistentCollection($this->dm, SimpleEntity::class);
        $e1 = new SimpleEntity(1, 2, 'one', 'two');
        $e2 = new SimpleEntity(1, 2, 'one', 'two');

        // Act
        $collection->add($e1);
        $collection->add($e2);
        $collection->remove($e1);

        // Assert
        $this->assertEquals(1, $collection->count());
        $this->assertTrue($collection->isChanged());
        $this->assertEquals(0, $collection->removedItems()->count());
        $this->assertEquals(1, $collection->addedItems()->count());
        $this->assertTrue($collection->addedItems()->contains($e2));
    }

    public function testRemovingItemsCorrectlyChangesStateWhenItemsWereFirstAddedInConstructor()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $e1 = new SimpleEntity(1, 2, 'one', 'two');
        $e2 = new SimpleEntity(1, 2, 'one', 'two');
        $e3 = new SimpleEntity(1, 2, 'one', 'two');
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, [
            $e1, $e2, $e3
        ]);

        // Act
        $collection->remove($e1);

        // Assert
        $this->assertEquals(2, $collection->count());
        $this->assertTrue($collection->isChanged());
        $this->assertEquals(1, $collection->removedItems()->count());
        $this->assertEquals(0, $collection->addedItems()->count());
        $this->assertTrue($collection->removedItems()->contains($e1));
    }

    public function testThatWhenRequestingUnloadedItemItLoadsItCorrectly()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se);
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, [$se->getId()]);

        // Act
        $loadedSe = $collection->first();

        // Assert
        $this->assertTrue($loadedSe instanceof SimpleEntity);
        $this->assertEquals($se->getId(), $loadedSe->getId());
    }

    public function testThatWhenRequestingArrayItLoadsAllItemsCorrectly()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $se2 = new SimpleEntity(1, 2, 'one', 'two');
        $se3 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $this->dm->persist($se2);
        $this->dm->persist($se3);
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, [
            $se1->getId(),
            $se2->getId(),
            $se3->getId(),
        ]);

        // Act
        $loadedArray = $collection->toArray();

        // Assert
        $this->assertEquals(3, sizeof($loadedArray));
        foreach ($loadedArray as $loadedSe)
        {
            $this->assertTrue($loadedSe instanceof SimpleEntity);
        }
    }

    public function testThatWhenRequestingIteratorItLoadsAllItemsCorrectly()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $se1 = new SimpleEntity(1, 2, 'one', 'two');
        $se2 = new SimpleEntity(1, 2, 'one', 'two');
        $se3 = new SimpleEntity(1, 2, 'one', 'two');
        $this->dm->persist($se1);
        $this->dm->persist($se2);
        $this->dm->persist($se3);
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, [
            $se1->getId(),
            $se2->getId(),
            $se3->getId(),
        ]);

        // Assert
        $counter = 0;
        foreach ($collection as $loadedSe)
        {
            $this->assertTrue($loadedSe instanceof SimpleEntity);
            $counter++;
        }
        $this->assertEquals(3, $counter);
    }

    public function testJsonEncodeWorksCorrectly()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, [
            $this->dm->persist(new SimpleEntity(1, 2, 'one1', 'two1')),
            $this->dm->persist(new SimpleEntity(1, 2, 'one2', 'two2')),
            $this->dm->persist(new SimpleEntity(1, 2, 'one3', 'two3')),
        ]);

        $encoded = json_encode($collection);
        $decoded = json_decode($encoded, true);

        // Assert
        $this->assertEquals(3, sizeof($decoded));
    }

    public function testGetIdList()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $ids = [];
        $firstHalf = [];
        $secondHalf = [];
        for ($i = 0; $i < 100; $i++)
        {
            $entity = new SimpleEntity($i, 2, 'one', 'two');
            $id = $this->dm->persist($entity);
            $ids[] = $id;

            if ($i < 50)
            {
                $firstHalf[] = $id;
                continue;
            }

            $secondHalf[] = $entity;
        }
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, $firstHalf);
        $collection->add($secondHalf);

        // Act
        $retrievedIds = $collection->getIdList();

        // Assert
        $this->assertEquals($ids, $retrievedIds);
    }

    /**
     * @expectedException Exception
     */
    public function testSortByWhenPassingANonExistantProperty()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $collection = new PersistentCollection($this->dm, SimpleEntity::class);

        // Act
        $collection->sortBy('rubbish');
    }

    public function testSortByWithUnloadedCollection()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $sortedIds = [];
        for ($i = 0; $i < 100; $i++)
        {
            $sortedIds[] = $this->dm->persist(new SimpleEntity($i, 2, 'one', 'two'));
        }
        $shuffledIds = $sortedIds;
        shuffle($shuffledIds);
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, $sortedIds);

        // Act
        $collection->sortBy('one');
        $retrievedIds = $collection->getIdList();

        // Assert
        $this->assertEquals($sortedIds, $retrievedIds);

        // Act
        $collection->sortBy('one', false);
        $retrievedIds = $collection->getIdList();
        rsort($sortedIds);

        // Assert
        $this->assertEquals($sortedIds, $retrievedIds);
    }

    public function testSortByWithHalfLoadedCollection()
    {
        // Arrange
        $this->setUpSimpleEntity();
        $sortedIds = [];
        $firstHalf = [];
        $secondHalf = [];
        for ($i = 0; $i < 100; $i++)
        {
            $entity = new SimpleEntity($i, 2, 'one', 'two');
            $sortedIds[] = $this->dm->persist($entity);

            if ($i < 50)
            {
                $firstHalf[] = $entity->getId();
                continue;
            }

            $secondHalf[] = $entity;
        }
        shuffle($firstHalf);
        $collection = new PersistentCollection($this->dm, SimpleEntity::class, $firstHalf);
        $collection->add($secondHalf);

        // Act
        $collection->sortBy('one');

        // Assert
        $this->assertEquals($sortedIds, $collection->getIdList());

        // Act
        $collection->sortBy('one', false);
        rsort($sortedIds);

        // Assert
        $this->assertEquals($sortedIds, $collection->getIdList());
    }
}