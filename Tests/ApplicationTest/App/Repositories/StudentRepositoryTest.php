<?php

namespace Tests\ApplicationTest\App\Repositories;

use App\Repositories\StudentRepository;
use Library\Facades\ModelFactory as Factory;
use Library\Facades\Sentry;
use Models\Activity;
use Models\ActivityStudent;
use Tests\ApplicationTest\BaseTest;
use Carbon\Carbon;

class StudentRepositoryTest extends BaseTest
{
    public function testAddNewStudent()
    {
        // Arrange
        $repository = new StudentRepository();
        $this->authenticateTeacher();
        $activity = Factory::of(Activity::class)->create();
        $data = [
            'firstName' => 'fname',
            'lastName' => 'lname',
            'email' => 'an@email.com',
            'activity' => $activity->id,
            'rate' => $activity->rate
        ];

        // Act
        $student = $repository->addNewStudent($data);
        $activityStudent = ActivityStudent::where('student_id', '=', $student->id)
            ->where('activity_id', '=', $activity->id)
            ->get()->first();

        // Assert
        $this->assertNotNull($student);
        $this->assertEquals(Sentry::id(), $student->teacher_id);
        $this->assertEquals('fname', $student->first_name);
        $this->assertEquals('lname', $student->last_name);
        $this->assertEquals('an@email.com', $student->email);
        $this->assertEquals($activity->rate, $activityStudent->rate);
        $this->assertEquals(Carbon::now()->day, $activityStudent->start_day);
    }
}