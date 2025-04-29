<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewerFileIssue extends Model
{
    protected $fillable = [
        'file_type',
        'file',
        'issue_id',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
    
}
