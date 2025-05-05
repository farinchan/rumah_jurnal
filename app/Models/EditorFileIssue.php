<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EditorFileIssue extends Model
{
    protected $guarded = [
        'id',
        'created_at',
        'updated_at',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class);
    }
}
