<?php namespace Tests\SchoolTest\Modules\Teacher\Ajax;

use Tests\SchoolTest\BaseTest;
use Applications\School\Modules\Teacher\Ajax\AjaxController;

class AjaxControllerTest extends BaseTest
{
	public function testAddEvent()
	{
		// Arrange
		$controller = new AjaxController();
		$_POST['isAllDay'] = true;

		// Act
		ob_start();
		$controller->addEvent();
		$result = ob_get_clean();

		// Assert
		$this->assertEquals('1', $result);
	}
}