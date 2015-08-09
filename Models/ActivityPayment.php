<?php

namespace Models;

use Library\Database\Model;

class ActivityPayment extends Model
{
    protected $fillable = [
        'teacher_id', 
        'student_id',
        'activity_id',
        'due_date',
        'payment_date',
        'due_amount',
        'amount',
        'method'
    ];
}