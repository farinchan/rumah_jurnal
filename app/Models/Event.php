<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Event extends Model
{

    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logFillable()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }

    protected $table = 'event';
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getThumbnail()
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : asset('back/media/svg/files/blank-image.svg');
    }

    public function getAttachment()
    {
        return $this->attachment ? Storage::url($this->file) : null;
    }

    public function users()
    {
        return $this->hasMany(EventUser::class);
    }

}
