<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class WelcomeSpeech extends Model
{
    use HasFactory,  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}");
    }


    protected $table = 'welcome_speech';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function getImage()
    {
        return Storage::url($this->image);
    }
}
