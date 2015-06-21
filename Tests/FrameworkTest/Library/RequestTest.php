<?php namespace Tests\FrameworkTest\Library;

use Library\Request;
use Tests\FrameworkTest\BaseTest;

class RequestTest extends BaseTest
{
    public function testThatGetRequestDataCanBeAccessedByMagicGetter()
    {
        // Arrange
        $request = new Request();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['one'] = 1;
        $_GET['two'] = 2;
        $_GET['three'] = 'text';

        // Act
        $one = $request->one;
        $two = $request->two;
        $three = $request->three;

        // Assert
        $this->assertEquals(1, $one);
        $this->assertEquals(2, $two);
        $this->assertEquals('text', $three);
    }

    public function testThatPostRequestDataCanBeAccessedByMagicGetter()
    {
        // Arrange
        $request = new Request();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['one'] = 1;
        $_POST['two'] = 2;
        $_POST['three'] = 'text';

        // Act
        $one = $request->one;
        $two = $request->two;
        $three = $request->three;

        // Assert
        $this->assertEquals(1, $one);
        $this->assertEquals(2, $two);
        $this->assertEquals('text', $three);
    }

    public function testThatAllRequestValuesCanBeReturnedAtOnceForGet()
    {
        // Arrange
        $request = new Request();
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_GET['one'] = 1;
        $_GET['two'] = 2;
        $_GET['three'] = 'text';

        // Act
        $results = $request->all();

        // Assert
        $this->assertEquals(3, sizeof($results));
        $this->assertEquals(1, $results['one']);
        $this->assertEquals(2, $results['two']);
        $this->assertEquals('text', $results['three']);
    }

    public function testThatAllRequestValuesCanBeReturnedAtOnceForPost()
    {
        // Arrange
        $request = new Request();
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['one'] = 1;
        $_POST['two'] = 2;
        $_POST['three'] = 'text';

        // Act
        $results = $request->all();

        // Assert
        $this->assertEquals(3, sizeof($results));
        $this->assertEquals(1, $results['one']);
        $this->assertEquals(2, $results['two']);
        $this->assertEquals('text', $results['three']);
    }
}