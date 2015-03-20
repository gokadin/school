<?php namespace Tests\FrameworkTest\Database;

use Library\Facades\DB;
use Tests\FrameworkTest\BaseTest;

class DatabaseTest extends BaseTest
{
    public function testThatTablesAreProperlyFound()
    {
        // Arrange
        $test = DB::table('test');

        // Assert
        $this->assertNotNull($test);
    }
}