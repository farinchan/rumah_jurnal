<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Editor extends Model
{
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
}
