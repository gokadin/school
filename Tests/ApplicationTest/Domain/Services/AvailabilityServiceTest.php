<?php

namespace Tests\ApplicationTest\Domain\Services;

use App\Domain\Activities\Activity;
use App\Domain\Calendar\Availability;
use App\Domain\Common\Address;
use App\Domain\Events\Event;
use App\Domain\School\School;
use App\Domain\Services\AvailabilityService;
use App\Domain\Setting\TeacherSettings;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\Student;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class AvailabilityServiceTest extends ServiceTest
{
    public function setUp()
    {
        parent::setUp();

        $this->setUpService(AvailabilityService::class, [
            Teacher::class,
            Subscription::class,
            Activity::class,
            Student::class,
            TeacherSettings::class,
            Event::class,
            Availability::class,
            Address::class,
            School::class
        ]);

        $this->setUpTeacher();
    }

    public function tearDown()
    {
        parent::tearDown();
    }

    public function test_fetch_shouldReturnEmptyIfThereIsNoRecord()
    {
        // Act
        //$availabilities = $this->service->fetch($this->teacher, Carbon::now()->startOfWeek()->subDay());

        // Assert
        //$this->assertEquals(0, sizeof($availabilities));
        $this->assertTrue(false);
    }

    public function test_fetch_shouldReturnNonDefaultRecordAtGivenDateIfItExists()
    {
        // Arrange

        // Act

        // Assert
        $this->assertTrue(false);
    }

    public function test_fetch_shouldReturnLastDefaultWeekBeforeGivenDateIfThereIsNoNonDefaultRecordForThatDate()
    {
        // Arrange

        // Act

        // Assert
        $this->assertTrue(false);
    }
}