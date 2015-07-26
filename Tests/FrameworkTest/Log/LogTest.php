<?php

namespace Tests\FrameworkTest\Log;

use Tests\FrameworkTest\BaseTest;
use Library\Facades\Log;

class LogTest extends BaseTest
{
    public function testSetLogFolder()
    {
        // Act
        Log::setLogFolder('Test/Folder');

        // Assert
        $this->assertEquals('Test/Folder', Log::getLogFolder());
    }

    public function testSetLogFolderWithEndingSlash()
    {
        // Act
        Log::setLogFolder('Test/Folder/');

        // Assert
        $this->assertEquals('Test/Folder', Log::getLogFolder());
    }

    public function testLogFileNameIsCorrectlyGenerated()
    {
        // Arrange
        Log::setLogFolder('Tests/FrameworkTest/TestData/Logs');
        $expectedFileName = __DIR__.'/../TestData/Logs/log-'.date('d-m-Y');

        // Act
        Log::info('testing');

        // Assert
        $this->assertTrue(file_exists($expectedFileName));

        // Act
        unlink($expectedFileName);
    }
}