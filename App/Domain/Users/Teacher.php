<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use App\Domain\Calendar\Availability;
use App\Domain\Events\Event;
use App\Domain\Setting\TeacherSettings;
use Library\DataMapper\Collection\EntityCollection;
use App\Domain\Common\Address;
use Library\DataMapper\Collection\PersistentCollection;

/**
 * @Entity(name="teachers")
 */
class Teacher extends User
{
    /** @HasOne(target="App\Domain\Subscriptions\Subscription") */
    protected $subscription;

    /** @HasOne(target="App\Domain\Setting\TeacherSettings") */
    private $settings;

    /** @HasMany(target="App\Domain\Activities\Activity", mappedBy="activity") */
    protected $activities;

    /** @HasMany(target="App\Domain\Users\Student", mappedBy="teacher") */
    private $students;

    /** @HasMany(target="App\Domain\Events\Event", mappedBy="teacher") */
    private $events;

    /** @HasMany(target="App\Domain\Calendar\WeekAvailability", mappedBy="teacher", nullable) */
    private $weekAvailabilities;

    public function __construct($firstName, $lastName, $email, $password, $subscription,
                                Address $address, $school, TeacherSettings $settings)
    {
        parent::__construct($firstName, $lastName, $email, $password, $address, $school);

        $this->subscription = $subscription;
        $this->settings = $settings;
        $this->activities = new EntityCollection();
        $this->students = new EntityCollection();
        $this->availabilities = new EntityCollection();
    }

    public function subscription()
    {
        return $this->subscription;
    }

    public function setSubscription($subscription)
    {
        $this->subscription = $subscription;
    }

    public function settings()
    {
        return $this->settings;
    }

    public function setSettings($settings)
    {
        $this->settings = $settings;
    }

    /**
     * @return PersistentCollection
     */
    public function activities()
    {
        return $this->activities;
    }

    public function addActivity(Activity $activity)
    {
        $this->activities->add($activity);
    }

    public function removeActivity(Activity $activity)
    {
        $this->activities->remove($activity);
    }

    /**
     * @return PersistentCollection
     */
    public function students()
    {
        return $this->students;
    }

    public function addStudent(Student $student)
    {
        $this->students->add($student);
    }

    public function removeStudent(Student $student)
    {
        $this->students->remove($student);
    }

    /**
     * @return PersistentCollection
     */
    public function events()
    {
        return $this->events;
    }

    public function addEvent(Event $event)
    {
        $this->events->add($event);
    }

    public function removeEvent(Event $event)
    {
        $this->events->remove($event);
    }

    /**
     * @return PersistentCollection
     */
    public function weekAvailabilities()
    {
        return $this->weekAvailabilities;
    }

    public function addWeekAvailability(Availability $availability)
    {
        $this->availabilities->add($availability);
    }

    public function removeWeekAvailability(Availability $availability)
    {
        $this->availabilities->remove($availability);
    }
}