<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceYear extends Model
{
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'distribution_percentage',
        'is_active',
        'created_by',
        'type_control',
    ];
}
