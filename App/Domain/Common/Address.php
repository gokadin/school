<?php

namespace App\Domain\Common;

use Library\DataMapper\DataMapperPrimaryKey;
use Library\DataMapper\DataMapperTimestamps;

/**
 * @Entity(name="addresses")
 */
class Address
{
    use DataMapperPrimaryKey, DataMapperTimestamps;

    /** @Column(type="string", nullable) */
    protected $country;

    /** @Column(type="string", nullable) */
    protected $state;

    /** @Column(type="string", nullable) */
    protected $city;

    /** @Column(type="string", nullable) */
    protected $postalCode;

    /** @Column(type="string", nullable) */
    protected $street;

    /** @Column(type="string", nullable) */
    protected $civicNumber;

    /** @Column(type="string", nullable) */
    protected $appNumber;

    public function country()
    {
        return $this->country;
    }

    public function setCountry($country)
    {
        $this->country = $country;
    }

    public function state()
    {
        return $this->state;
    }

    public function setState($state)
    {
        $this->state = $state;
    }

    public function city()
    {
        return $this->city;
    }

    public function setCity($city)
    {
        $this->city = $city;
    }

    public function postalCode()
    {
        return $this->postalCode;
    }

    public function setPostalCode($postalCode)
    {
        $this->postalCode = $postalCode;
    }

    public function street()
    {
        return $this->street;
    }

    public function setStreet($street)
    {
        $this->street = $street;
    }

    public function civicNumber()
    {
        return $this->civicNumber;
    }

    public function setCivicNumber($civicNumber)
    {
        $this->civicNumber = $civicNumber;
    }

    public function appNumber()
    {
        return $this->appNumber;
    }

    public function setAppNumber($appNumber)
    {
        $this->appNumber = $appNumber;
    }
}