<?php

namespace Tests\FrameworkTest\Log;

use Tests\FrameworkTest\BaseTest;
use Library\Log\Log;

class LogTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        date_default_timezone_set('America/Montreal');
    }

    public function testSetLogFolder()
    {
        // Arrange
        $log = new Log();

        // Act
        $log->setLogFolder('TestData/Logs');

        // Assert
        $this->assertEquals('TestData/Logs', $log->getLogFolder());
    }

    public function testSetLogFolderWithEndingSlash()
    {
        // Arrange
        $log = new Log();

        // Act
        $log->setLogFolder('TestData/Logs/');

        // Assert
        $this->assertEquals('TestData/Logs', $log->getLogFolder());
    }

    public function testLogFileNameIsCorrectlyGenerated()
    {
        // Arrange
        $log = new Log('TestData/Logs');
        $expectedFileName = 'TestData/Logs/log-'.date('d-m-Y');

        // Act
        $log->info('testing');

        // Assert
        $this->assertTrue(file_exists($expectedFileName));

        // Act
        unlink($expectedFileName);
    }
}