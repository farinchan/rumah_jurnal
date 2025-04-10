<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class WelcomeSpeech extends Model
{
    use HasFactory;


    protected $table = 'welcome_speech';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function getImage()
    {
        return Storage::url($this->image);
    }
}
