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

    
}
