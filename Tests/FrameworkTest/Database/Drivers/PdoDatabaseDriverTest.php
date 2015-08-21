<?php

namespace FrameworkTest\Database\Drivers;

use Library\Database\Drivers\PdoDatabaseDriver;
use Tests\FrameworkTest\BaseTest;

class PdoDatabaseDriverTest extends BaseTest
{
    protected $driver;

    public function setUp()
    {
        parent::setUp();

        $this->driver = new PdoDatabaseDriver([
            'host' => 'localhost',
            'database' => 'FrameworkTest',
            'username' => 'root',
            'password' => 'f10ygs87'
        ]);

        $this->driver->beginTransaction();
    }

    public function tearDown()
    {
        $this->driver->rollBack();
    }

    public function testInsert()
    {
        $this->driver->insert()
    }
}