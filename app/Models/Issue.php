<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Issue extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'issue_id');
    }

    public function reviewers()
    {
        return $this->hasMany(Reviewer::class, 'issue_id');
    }
}
