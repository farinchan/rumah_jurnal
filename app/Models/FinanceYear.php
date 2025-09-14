<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];
}
