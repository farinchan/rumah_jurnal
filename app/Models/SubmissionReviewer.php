<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class SubmissionReviewer extends Model
{
    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }
    protected $table = 'submission_reviewer';

    protected $fillable = [
        'submission_id',
        'reviewer_id',
        'created_at',
        'updated_at',
    ];

    public function submission()
    {
        return $this->belongsTo(Submission::class, 'submission_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
