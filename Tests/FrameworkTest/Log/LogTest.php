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
        $log->setLogFolder('Test/Folder');

        // Assert
        $this->assertEquals('Test/Folder', $log->getLogFolder());
    }

    public function testSetLogFolderWithEndingSlash()
    {
        // Arrange
        $log = new Log();

        // Act
        $log->setLogFolder('Test/Folder/');

        // Assert
        $this->assertEquals('Test/Folder', $log->getLogFolder());
    }

    public function testLogFileNameIsCorrectlyGenerated()
    {
        // Arrange
        $log = new Log();
        $log->setLogFolder('Tests/FrameworkTest/TestData/Logs');
        $expectedFileName = __DIR__.'/../TestData/Logs/log-'.date('d-m-Y');

        // Act
        $log->info('testing');

        // Assert
        $this->assertTrue(file_exists($expectedFileName));

        // Act
        unlink($expectedFileName);
    }
}