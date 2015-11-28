<?php

namespace FrameworkTest\DataMapper;

use Tests\FrameworkTest\DataMapper\DataMapperBaseTest;
use Tests\FrameworkTest\TestData\DataMapper\SimpleEntity;

class ExamplesTest extends DataMapperBaseTest
{
    // SIMPLE

    public function testInsertNew()
    {
        $this->setUpSimpleEntity();

        $s1 = new SimpleEntity(1, 2, '1', '2');
        $this->dm->persist($s1);
        $this->dm->flush();

        $entity = $this->dm->find(SimpleEntity::class, 1);
    }

    public function testUpdate()
    {
        $this->setUpSimpleEntity();

        $s1 = $this->dm->find(SimpleEntity::class, 1);

        $s1->setName('name');

        $this->dm->flush();
    }

    public function testDelete()
    {
        $this->setUpSimpleEntity();

        $s1 = $this->dm->find(SimpleEntity::class, 1);

        $this->dm->delete($s1);

        $this->dm->flush();
    }
}