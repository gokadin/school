<?php

namespace ApplicationTest\Domain\Settings;

use App\Domain\Setting\StudentRegistrationForm;
use Tests\ApplicationTest\BaseTest;

class StudentRegistrationFormTest extends BaseTest
{
    public function testMakeFromJson()
    {
        // NOT CORRECT !!!
        // Arrange
        $json = StudentRegistrationForm::defaultJson();

        // Act
        $form = StudentRegistrationForm::makeFromJson($json);

        // Assert
        $this->assertTrue($form instanceof StudentRegistrationForm);
    }
}