<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EventAttendance extends Model
{
    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }
    protected $table = 'event_attendances';


    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    public function attendances()
    {
        return $this->hasMany(EventAttendanceUser::class, 'event_attendance_id');
    }

}
