<?php namespace Models;

use Library\Database\Model;

class TeacherEvent extends Model
{
    protected $fillable = [
        'teacher_id',
        'title',
        'start_date',
        'end_date',
        'is_all_day',
        'start_time',
        'end_time',
        'is_recurring',
        'recurring_repeat',
        'recurring_every',
        'is_recurring_ends_never',
        'recurring_end_date',
        'description',
        'color',
        'location',
        'visibility',
        'student_ids',
        'activity_id',
        'notify_me_by',
        'notify_me_before'
    ];

    public function teacher()
    {
        return $this->belongsTo('Teacher');
    }
}
