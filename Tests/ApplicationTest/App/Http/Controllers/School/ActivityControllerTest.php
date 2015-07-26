<?php

namespace Tests\ApplicationTest\App\Http\Controllers\School;

use Library\Facades\Redirect;
use Models\Activity;
use Tests\ApplicationTest\BaseTest;

class ActivityControllerTest extends BaseTest
{
    public function setUp()
    {
        parent::setUp();

        $this->authenticateTeacher();
    }

    public function testCreateWhenValidAndCreateAnotherIsChecked()
    {
        // Arrange
        $this->beginDatabaseTransaction();

        Redirect::shouldReceive('to')
            ->once()
            ->with('school.teacher.activity.create');

        // Act
        $this->post('school.teacher.activity.store', [
            'createAnother' => 1,
            'name' => 'name1',
            'defaultRate' => 50,
            'period' => 1,
            'location' => 'location1'
        ]);
        $activity = Activity::where('name', '=', 'name1')
            ->where('rate', '=', 50)
            ->get()
            ->first();

        // Assert
        $this->assertNotNull($activity);
        $this->assertEquals('name1', $activity->name);
        $this->assertEquals(50, $activity->rate);
        $this->assertEquals(1, $activity->period);
        $this->assertEquals('location1', $activity->location);
    }

    public function testCreateWhenValidAndCreateAnotherIsUnchecked()
    {
        // Arrange
        $this->beginDatabaseTransaction();

        Redirect::shouldReceive('to')
            ->once()
            ->with('school.teacher.activity.index');

        // Act
        $this->post('school.teacher.activity.store', [
            'createAnother' => 0,
            'name' => 'name1',
            'defaultRate' => 50,
            'period' => 1,
            'location' => 'location1'
        ]);
        $activity = Activity::where('name', '=', 'name1')
            ->where('rate', '=', 50)
            ->get()
            ->first();

        // Assert
        $this->assertNotNull($activity);
        $this->assertEquals('name1', $activity->name);
        $this->assertEquals(50, $activity->rate);
        $this->assertEquals(1, $activity->period);
        $this->assertEquals('location1', $activity->location);
    }
}