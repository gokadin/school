<?php

namespace Tests\ApplicationTest\Domain\Services;

use App\Domain\Activities\Activity;
use App\Domain\Calendar\Availability;
use App\Domain\Calendar\WeekAvailability;
use App\Domain\Common\Address;
use App\Domain\Events\Event;
use App\Domain\School\School;
use App\Domain\Services\AvailabilityService;
use App\Domain\Setting\TeacherSettings;
use App\Domain\Subscriptions\Subscription;
use App\Domain\Users\Student;
use App\Domain\Users\Teacher;
use Carbon\Carbon;

class AvailabilityServiceTest extends ServiceTestBase
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
            WeekAvailability::class,
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
        // Arrange
        $this->teacher = $this->dm->find(Teacher::class, $this->teacher->getId());

        // Act
        $availabilities = $this->service->fetch($this->teacher, Carbon::now()->startOfWeek()->subDay());

        // Assert
        $this->assertEquals(0, sizeof($availabilities));
    }

    public function test_fetch_shouldReturnNonDefaultRecordAtGivenDateIfItExists()
    {
        // Arrange
        $dateWeWant = Carbon::now()->startOfWeek()->subDay();
        $defaultWeekAvailability = new WeekAvailability($this->teacher, $dateWeWant);
        $defaultWeekAvailability->setAsDefault();
        $nonDefaultWeekAvailability = new WeekAvailability($this->teacher, $dateWeWant);
        $availability = new Availability($dateWeWant, 100, 200);
        $nonDefaultWeekAvailability->setJsonData(json_encode([$availability->jsonSerialize()]));
        $this->dm->persist($defaultWeekAvailability);
        $this->dm->persist($nonDefaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($defaultWeekAvailability);
        $this->teacher->addWeekAvailability($nonDefaultWeekAvailability);

        // Act
        $availabilities = $this->service->fetch($this->teacher, $dateWeWant);

        // Assert
        $this->assertEquals(1, sizeof($availabilities));
        $this->assertEquals($availability->uniqueId(), $availabilities[0]->uniqueId());
    }

    public function test_fetch_shouldReturnLastDefaultWeekBeforeGivenDateIfThereIsNoNonDefaultRecordForThatDate()
    {
        // Arrange
        $dateWeWant = Carbon::now()->startOfWeek()->subDay();

        $defaultWeWant = new WeekAvailability($this->teacher, $dateWeWant);
        $defaultWeWant->setAsDefault();
        $availability = new Availability($dateWeWant, 100, 200);
        $defaultWeWant->setJsonData(json_encode([$availability]));
        $this->dm->persist($defaultWeWant);

        $defaultOlder = new WeekAvailability($this->teacher,
            Carbon::now()->startOfWeek()->subDay()->subWeek(10));
        $defaultOlder->setAsDefault();
        $this->dm->persist($defaultOlder);

        $nonDefaultWeekAvailability = new WeekAvailability($this->teacher,
            Carbon::now()->startOfWeek()->subDay()->addWeek());
        $this->dm->persist($nonDefaultWeekAvailability);

        $this->dm->flush();
        $this->teacher->addWeekAvailability($defaultWeWant);
        $this->teacher->addWeekAvailability($nonDefaultWeekAvailability);
        $this->teacher->addWeekAvailability($defaultOlder);

        // Act
        $availabilities = $this->service->fetch($this->teacher, $dateWeWant);

        // Assert
        $this->assertEquals(1, sizeof($availabilities));
        $this->assertEquals($availability->uniqueId(), $availabilities[0]->uniqueId());
    }

    public function test_store_whenANonDefaultWeekAvailabilityExistsForThatWeekItShouldAddToIt()
    {
        // Arrange
        $weekStartDate = Carbon::now()->startOfWeek()->subDay();
        $nonDefaultWeekAvailability = new WeekAvailability($this->teacher, $weekStartDate);
        $this->dm->persist($nonDefaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($nonDefaultWeekAvailability);

        $a1 = new Availability(Carbon::now()->startOfWeek(), 100, 200);
        $a2 = new Availability(Carbon::now()->startOfWeek()->addDays(2), 100, 200);

        // Act
        $a1UniqueId = $this->service->store($this->teacher, $a1);
        $a2UniqueId = $this->service->store($this->teacher, $a2);

        $availabilities = $nonDefaultWeekAvailability->availabilities();

        // Assert
        $this->assertEquals($a1->uniqueId(), $a1UniqueId);
        $this->assertEquals($a2->uniqueId(), $a2UniqueId);
        $this->assertEquals(2, sizeof($availabilities));
    }

    public function test_store_whenNonDefaultAndDefaultWeekAvailabilitiesDoNotExistForThatWeekThenItShouldCreateANonDefaultOneAndAddToIt()
    {
        // Arrange
        $a1 = new Availability(Carbon::now()->startOfWeek()->subDay()->addDays(3), 100, 200);

        // Act
        $uniqueId = $this->service->store($this->teacher, $a1);

        $weekAvailability = $this->dm->findAll(WeekAvailability::class)->first();

        // Assert
        $this->assertEquals($a1->uniqueId(), $uniqueId);
        $this->assertNotNull($weekAvailability);
        $this->assertEquals(1, sizeof($weekAvailability->availabilities()));
        $this->assertEquals($a1->uniqueId(), $weekAvailability->availabilities()[0]['uniqueId']);
    }

    public function test_store_ifAvailabilityDateIsASundayItShouldAddToTheCorrectWeek()
    {
        // Arrange
        $a1 = new Availability(Carbon::now()->startOfWeek()->subDay(), 100, 200);

        // Act
        $this->service->store($this->teacher, $a1);

        $weekAvailability = $this->dm->findAll(WeekAvailability::class)->first();

        // Assert
        $this->assertNotNull($weekAvailability);
        $this->assertEquals(1, sizeof($weekAvailability->availabilities()));
        $this->assertEquals($a1->date()->toDateString(), Carbon::parse($weekAvailability->weekStartDate())->toDateString());
    }

    public function test_store_whenNonDefaultDoesNotExistItShouldCreateItFromTheExistingDefaultTemplateAndAddToIt()
    {
        // Arrange
        $weekStartDate = Carbon::now()->startOfWeek()->subDay();
        $a1 = new Availability($weekStartDate->copy()->addDays(3), 100, 200);

        $a2 = new Availability($weekStartDate->copy()->subWeeks(3)->addDays(2), 300, 400);
        $defaultWeekAvailability = new WeekAvailability($this->teacher, $weekStartDate->copy()->subWeeks(3));
        $defaultWeekAvailability->setAsDefault();
        $defaultWeekAvailability->addAvailability($a2);
        $this->dm->persist($defaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($defaultWeekAvailability);

        // Act
        $uniqueId = $this->service->store($this->teacher, $a1);

        $weekAvailability = $this->dm->findOneBy(WeekAvailability::class, ['weekStartDate' => $weekStartDate->toDateString()]);

        // Assert
        $this->assertEquals($a1->uniqueId(), $uniqueId);
        $this->assertNotNull($weekAvailability);
        $this->assertEquals(2, sizeof($weekAvailability->availabilities()));
    }

    public function test_update_whenNonDefaultExists()
    {
        // Arrange
        $weekStartDate = Carbon::now()->startOfWeek()->subDay();
        $nonDefaultWeekAvailability = new WeekAvailability($this->teacher, $weekStartDate);
        $aShouldNotUpdate = new Availability($weekStartDate, 100, 200);
        $aShouldUpdate = new Availability($weekStartDate, 300, 400);
        $nonDefaultWeekAvailability->addAvailability($aShouldNotUpdate);
        $nonDefaultWeekAvailability->addAvailability($aShouldUpdate);
        $this->dm->persist($nonDefaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($nonDefaultWeekAvailability);

        $aUpdated = new Availability($weekStartDate->copy()->addDay(), 350, 550);
        $aUpdated->setUniqueId(2);

        // Act
        $this->service->update($this->teacher, $aUpdated);

        // Assert
        $this->assertEquals($weekStartDate->toDateString(), $nonDefaultWeekAvailability->availabilities()[0]['date']);
        $this->assertEquals(100, $nonDefaultWeekAvailability->availabilities()[0]['startTime']);
        $this->assertEquals(200, $nonDefaultWeekAvailability->availabilities()[0]['endTime']);
        $this->assertEquals($weekStartDate->copy()->addDay()->toDateString(), $nonDefaultWeekAvailability->availabilities()[1]['date']);
        $this->assertEquals(350, $nonDefaultWeekAvailability->availabilities()[1]['startTime']);
        $this->assertEquals(550, $nonDefaultWeekAvailability->availabilities()[1]['endTime']);
    }

    public function test_update_whenOnlyDefaultExists()
    {
        // Arrange
        $weekStartDate = Carbon::now()->startOfWeek()->subDay();
        $defaultWeekAvailability = new WeekAvailability($this->teacher, $weekStartDate->copy()->subWeeks(3));
        $defaultWeekAvailability->setAsDefault();
        $aShouldNotUpdate = new Availability($weekStartDate->copy()->subWeeks(3), 100, 200);
        $aShouldUpdate = new Availability($weekStartDate->copy()->subWeeks(3), 300, 400);
        $defaultWeekAvailability->addAvailability($aShouldNotUpdate);
        $defaultWeekAvailability->addAvailability($aShouldUpdate);
        $this->dm->persist($defaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($defaultWeekAvailability);

        $aUpdated = new Availability($weekStartDate, 300, 550);
        $aUpdated->setUniqueId(2);

        // Act
        $this->service->update($this->teacher, $aUpdated);

        $weekAvailability = $this->dm->findOneBy(WeekAvailability::class, ['weekStartDate' => $weekStartDate->toDateString()]);

        // Assert
        $this->assertNotNull($weekAvailability);
        $this->assertEquals(2, sizeof($weekAvailability->availabilities()));
        $this->assertEquals($weekStartDate->toDateString(), $weekAvailability->availabilities()[0]['date']);
        $this->assertEquals(100, $weekAvailability->availabilities()[0]['startTime']);
        $this->assertEquals(200, $weekAvailability->availabilities()[0]['endTime']);
        $this->assertEquals($weekStartDate->toDateString(), $weekAvailability->availabilities()[1]['date']);
        $this->assertEquals(300, $weekAvailability->availabilities()[1]['startTime']);
        $this->assertEquals(550, $weekAvailability->availabilities()[1]['endTime']);
    }

    public function test_destroy_whenNonDefaultExists()
    {
        // Arrange
        $weekStartDate = Carbon::now()->startOfWeek()->subDay();
        $nonDefaultWeekAvailability = new WeekAvailability($this->teacher, $weekStartDate);
        $aShouldNotDelete = new Availability($weekStartDate, 100, 200);
        $aShouldDelete = new Availability($weekStartDate, 300, 400);
        $nonDefaultWeekAvailability->addAvailability($aShouldNotDelete);
        $nonDefaultWeekAvailability->addAvailability($aShouldDelete);
        $this->dm->persist($nonDefaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($nonDefaultWeekAvailability);

        // Act
        $this->service->destroy($this->teacher, $aShouldDelete);

        // Assert
        $this->assertEquals(1, sizeof($nonDefaultWeekAvailability->availabilities()));
        $this->assertEquals(1, $nonDefaultWeekAvailability->availabilities()[0]['uniqueId']);
    }

    public function test_destroy_whenOnlyDefaultExists()
    {
        // Arrange
        $weekStartDate = Carbon::now()->startOfWeek()->subDay();
        $defaultWeekAvailability = new WeekAvailability($this->teacher, $weekStartDate->copy()->subWeeks(3));
        $defaultWeekAvailability->setAsDefault();
        $aShouldNotDelete = new Availability($weekStartDate->copy()->subWeeks(3), 100, 200);
        $aShouldDelete = new Availability($weekStartDate->copy()->subWeeks(3), 300, 400);
        $defaultWeekAvailability->addAvailability($aShouldNotDelete);
        $defaultWeekAvailability->addAvailability($aShouldDelete);
        $this->dm->persist($defaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($defaultWeekAvailability);

        // Act
        $temp = new Availability($weekStartDate, 0, 0);
        $temp->setUniqueId($aShouldDelete->uniqueId());
        $this->service->destroy($this->teacher, $temp);

        $weekAvailability = $this->dm->findOneBy(WeekAvailability::class, ['weekStartDate' => $weekStartDate->toDateString()]);

        // Assert
        $this->assertNotNull($weekAvailability);
        $this->assertEquals(1, sizeof($weekAvailability->availabilities()));
        $this->assertEquals($weekStartDate->toDateString(), $weekAvailability->availabilities()[0]['date']);
    }

    public function test_applyToFutureWeeks_correctlyCopiesCurrentWeekToADefaultTempalteForTheSameWeek()
    {
        // Arrange
        $weekStartDate = Carbon::now()->startOfWeek()->subDay();
        $nonDefaultWeekAvailability = new WeekAvailability($this->teacher, $weekStartDate);
        $a1 = new Availability($weekStartDate->copy()->addDay(), 100, 200);
        $a2 = new Availability($weekStartDate->copy()->addDays(3), 300, 400);
        $nonDefaultWeekAvailability->addAvailability($a1);
        $nonDefaultWeekAvailability->addAvailability($a2);
        $this->dm->persist($nonDefaultWeekAvailability);
        $this->dm->flush();
        $this->teacher->addWeekAvailability($nonDefaultWeekAvailability);

        // Act
        $this->service->applyToFutureWeeks($this->teacher, $weekStartDate);

        $default = $this->dm->findOneBy(WeekAvailability::class,
            ['weekStartDate' => $weekStartDate->toDateString(), 'isDefault' => true]);

        // Assert
        $this->assertNotNull($default);
        $this->assertEquals(2, sizeof($default->availabilities()));
    }
}