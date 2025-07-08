<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SubmissionEditor extends Model
{

    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }
    protected $table = 'submission_editor';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class);
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'editor_id');
    }
}
