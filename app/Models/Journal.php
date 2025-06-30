<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Journal extends Model
{
    use SoftDeletes;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'indexing' => 'array'
    ];

    protected $dates = ['deleted_at'];

    public function getContextUrl()
    {
        return $this->url . '/api/v1/contexts/' . $this->context_id;
    }

    public function getJournalThumbnail()
    {
        $base_url = parse_url($this->url, PHP_URL_SCHEME) . '://' . parse_url($this->url, PHP_URL_HOST);
        return $this->thumbnail ? $base_url . '/public/journals/' . $this->context_id . '/' . $this->thumbnail : 'https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg';
    }

    public function getEditorChiefSignature()
    {
        if ($this->editor_chief_signature) {
            return asset('storage/' . $this->editor_chief_signature);
        }else {
            return asset('back/media/svg/files/blank-image.svg');
        }
    }

    public function issues()
    {
        return $this->hasMany(Issue::class, 'journal_id');
    }



}
