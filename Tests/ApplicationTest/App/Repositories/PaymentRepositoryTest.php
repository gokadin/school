<?php

namespace Tests\ApplicationTest\App\Repositories;

use App\Repositories\PaymentRepository;
use Carbon\Carbon;
use Library\Facades\ModelFactory as Factory;
use Library\Facades\Sentry;
use Models\Activity;
use Models\ActivityPayment;
use Models\ActivityStudent;
use Models\Student;
use Tests\ApplicationTest\BaseTest;

class PaymentRepositoryTest extends BaseTest
{
    public function testInitiateNewStudentRecord()
    {
        // Arrange
        $activity = Factory::of(Activity::class)->create([
            'period' => 1
        ]);
        $student = Factory::of(Student::class)->create([
            'activity_id' => $activity->id
        ]);
        $activityStudent = Factory::of(ActivityStudent::class)->create([
            'activity_id' => $activity->id,
            'student_id' => $student->id
        ]);
        $repository = new PaymentRepository();
        $monthsInAdvance = PaymentRepository::MONTHS_IN_ADVANCE;

        // Act
        $repository->initiateNewStudentRecord($student);
        $records = ActivityPayment::where('student_id', '=', $student->id)
            ->where('activity_id', '=', $activity->id)
            ->get();

        // Assert
        $this->assertEquals($monthsInAdvance, $records->count());
        for ($i = 0; $i < $monthsInAdvance; $i++)
        {
            $this->assertEquals(Sentry::id(), $records->at($i)->teacher_id);
            $this->assertEquals($student->id, $records->at($i)->student_id);
            $this->assertEquals($activity->id, $records->at($i)->activity_id);
            $this->assertEquals($activityStudent->rate, $records->at($i)->due_amount);
            $this->assertFalse($records->at($i)->payment_date);
            $this->assertEquals(0, $records->at($i)->amount);

            $date = Carbon::now()->addMonth($i);
            $date->day = $activityStudent->start_day;
            $this->assertEquals($date->day, Carbon::parse($records->at($i)->due_date)->day);
            $this->assertEquals($date->month, Carbon::parse($records->at($i)->due_date)->month);
            $this->assertEquals($date->year, Carbon::parse($records->at($i)->due_date)->year);
        }
    }
}