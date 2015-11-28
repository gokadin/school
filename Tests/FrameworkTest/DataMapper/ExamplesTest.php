<?php

namespace FrameworkTest\DataMapper;

use Tests\FrameworkTest\DataMapper\DataMapperBaseTest;
use Tests\FrameworkTest\TestData\DataMapper\SimpleEntity;

class ExamplesTest extends DataMapperBaseTest
{
    // SIMPLE

    public function insertNew()
    {
        $this->setUpSimpleEntity();

        $s1 = new SimpleEntity(1, 2, '1', '2');
        $this->dm->persist($s1);
        $this->dm->flush();
    }

    public function update()
    {
        $this->setUpSimpleEntity();

        $s1 = $this->dm->find(SimpleEntity::class, 1);

        $s1->setName('name');

        $this->dm->flush();
    }

    public function delete()
    {
        $this->setUpSimpleEntity();

        $s1 = $this->dm->find(SimpleEntity::class, 1);

        $this->dm->delete($s1);

        $this->dm->flush();
    }
}