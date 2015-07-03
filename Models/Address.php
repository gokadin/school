<?php namespace Models;

use Library\Database\Model;

class Address extends Model
{
    protected $fillable = [
        'country',
        'state',
        'city',
        'postal_code',
        'street',
        'civic_number',
        'app_number'
    ];
}