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

    protected $fillable = [
        'country',
        'state',
        'city',
        'postal_code',
        'street',
        'civic_number',
        'app_number'
    ];

    /** @Column(type="string", nullable) */
    protected $country;
}