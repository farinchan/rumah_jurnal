<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'publication_keywords' => 'array',
        'publication_citations' => 'array'
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }


    public function getSubmissionFile()
    {
        $base_url = parse_url($this->issue->journal->url, PHP_URL_SCHEME) . '://' . parse_url($this->issue->journal->url, PHP_URL_HOST);
        return $this->file ? $base_url . '/public/submissions/' . $this->issue->journal->context_id . '/' . $this->issue->id . '/' . $this->id . '/' . $this->file : 'https://upload.wikimedia.org/wikipedia/commons/1/14/No_Image_Available.jpg';
    }
}
