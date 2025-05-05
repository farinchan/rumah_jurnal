<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionEditor extends Model
{
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
