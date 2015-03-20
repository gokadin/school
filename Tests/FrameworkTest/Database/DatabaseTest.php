<?php namespace Tests\FrameworkTest\Database;

use Library\Facades\DB;
use Tests\FrameworkTest\BaseTest;

class DatabaseTest extends BaseTest
{
    public function testThatTablesAreProperlyFound()
    {
        // Arrange
        $test = DB::table('activity');

        // Act

        // Assert
        $this->assertNotNull($test);
    }
}