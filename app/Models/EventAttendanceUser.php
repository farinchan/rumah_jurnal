<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAttendanceUser extends Model
{
    protected $table = 'event_attendance_users';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function eventAttendance()
    {
        return $this->belongsTo(EventAttendance::class, 'event_attendance_id');
    }

    public function eventUser()
    {
        return $this->belongsTo(EventUser::class, 'event_user_id');
    }

    /**
     * Get event through eventAttendance relationship
     */
    public function event()
    {
        return $this->hasOneThrough(
            Event::class,
            EventAttendance::class,
            'id', // Foreign key on event_attendances table
            'id', // Foreign key on event table
            'event_attendance_id', // Local key on event_attendance_users table
            'event_id' // Local key on event_attendances table
        );
    }
}
