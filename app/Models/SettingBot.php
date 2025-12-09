<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettingBot extends Model
{
    protected $table = 'setting_bots';

    protected $fillable = [
        'name',
        'api_production_url',
        'api_sandbox_url',
        'system_message',
        'additional_context',
        'signature',
        'is_active',
        'is_whatsapp_active',
    ];
}
