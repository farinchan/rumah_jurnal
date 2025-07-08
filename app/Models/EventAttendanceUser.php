<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventAttendanceUser extends Model
{
    protected $table = 'event_users';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
