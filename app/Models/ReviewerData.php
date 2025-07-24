<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class ReviewerData extends Model
{
    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logUnguarded()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "This model has been {$eventName}");
    }
    protected $table = 'reviewer_data';

    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    // public function reviewer()
    // {
    //     return $this->belongsTo(Reviewer::class, 'reviewer_id', 'reviewer_id');
    // }
}
