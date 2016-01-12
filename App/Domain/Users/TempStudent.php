<?php

namespace App\Domain\Users;

use App\Domain\Activities\Activity;
use Carbon\Carbon;
use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="temp_students")
 */
class TempStudent
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    const DAYS_BEFORE_EXPIRING = 7;

    /** @Column(type="string", nullable) */
    protected $firstName;

    /** @Column(type="string", nullable) */
    protected $lastName;

    /** @Column(type="string") */
    protected $email;

    /** @Column(type="decimal", size="5", precision="2") */
    private $customPrice;

    /** @Column(type="boolean") */
    private $hasAccount;

    /** @Column(type="string") */
    protected $confirmationCode;

    /** @BelongsTo(target="App\Domain\Users\Teacher") */
    private $teacher;

    /** @HasOne(target="App\Domain\Activities\Activity") */
    private $activity;

    public function __construct(Teacher $teacher, Activity $activity,
                                $firstName, $lastName, $email, $customPrice, $hasAccount, $confirmationCode)
    {
        $this->teacher = $teacher;
        $this->activity = $activity;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->customPrice = $customPrice;
        $this->hasAccount = $hasAccount;
        $this->confirmationCode = $confirmationCode;
    }

    public function firstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function lastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function email()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function customPrice()
    {
        return $this->customPrice;
    }

    public function setCustomPrice($customPrice)
    {
        $this->customPrice = $customPrice;
    }

    public function hasAccount()
    {
        return $this->hasAccount;
    }

    public function setHasAccount($hasAccount)
    {
        $this->hasAccount = $hasAccount;
    }

    public function confirmationCode()
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;
    }

    public function teacher()
    {
        return $this->teacher;
    }

    public function setTeacher(Teacher $teacher)
    {
        $this->teacher = $teacher;
    }

    public function activity()
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;
    }

    public function isExpired()
    {
        return $this->createdAt < Carbon::now()->subDays(self::DAYS_BEFORE_EXPIRING);
    }
}