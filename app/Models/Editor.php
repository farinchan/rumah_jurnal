<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Editor extends Model
{

    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'groups' => 'array',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function submissionsEdited()
    {
        return $this->hasMany(SubmissionEditor::class, 'editor_id');
    }

    public function journals()
    {
        return $this->hasManyThrough(
            Journal::class,   // model tujuan
            Issue::class,     // model perantara
            'id',             // foreign key di Issue untuk Editor → issue.id
            'id',             // primary key di Journal → journal.id
            'issue_id',       // foreign key di Editor → editor.issue_id
            'journal_id'      // foreign key di Issue → issue.journal_id
        );
    }

    public function data()
    {
        return $this->hasOne(EditorData::class, 'editor_id', 'editor_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'editor_id', 'editor_id');
    }
}
