<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EditorData extends Model
{
     use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}");
    }
    protected $table = 'editor_data';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function editor()
    {
        return $this->belongsTo(Editor::class, 'editor_id', 'editor_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'editor_id', 'editor_id');
    }
}
