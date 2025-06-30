<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }

    public function submissionsReviewed()
    {
        return $this->hasMany(SubmissionReviewer::class, 'reviewer_id');
    }
}


