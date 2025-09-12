<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Reviewer extends Model
{
    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function submissionsReviewed()
    {
        return $this->hasMany(SubmissionReviewer::class, 'reviewer_id');
    }

    public function  data()
    {
        return $this->hasOne(ReviewerData::class, 'reviewer_id', 'reviewer_id');
    }
}


