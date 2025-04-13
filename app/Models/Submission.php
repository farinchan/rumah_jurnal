<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $casts = [
        'authors' => 'array',
        'fullTitle' => 'array',
        'abstract' => 'array',
        'keywords' => 'array',
        'citations' => 'array',
    ];

    public function issue()
    {
        return $this->belongsTo(Issue::class, 'issue_id');
    }

    public function getTitleAttribute()
    {
        return $this->fullTitle[$this->locale] ?? '';
    }

    public function getAuthorsAttribute()
    {
        $authorsRaw = json_decode($this->attributes['authors'], true);

        $filtered = collect($authorsRaw)->map(function ($author) {
            return [
                'name' => ($author['givenName'][$this->locale] ?? '') . ' ' . ($author['familyName'][$this->locale] ?? ''),
                'email' => $author['email'] ?? '',
                'affiliation' => $author['affiliation'][$this->locale] ?? '-',
            ];
        });
        return $filtered->values()->all();
    }

    public function getAbstractAttribute()
    {
        $abstract = json_decode($this->attributes['abstract'], true);
        return $abstract[$this->attributes['locale']] ?? '';
    }
    public function getKeywordsAttribute()
    {
        $keywords = json_decode($this->attributes['keywords'], true);
        return implode(', ', $keywords[$this->attributes['locale']] ?? []);
    }
}
