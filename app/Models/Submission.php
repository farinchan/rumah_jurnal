<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Submission extends Model
{
    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }
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

    public function paymentInvoices()
    {
        return $this->hasMany(PaymentInvoice::class, 'submission_id');
    }

    public function getFullTitleAttribute()
    {
        $fullTitleRaw = json_decode($this->attributes['fullTitle'], true);

        return $fullTitleRaw[$this->attributes['locale']] ?? '';
    }

    public function getAuthorsAttribute()
    {
        $authorsRaw = json_decode($this->attributes['authors'], true);

        $filtered = collect($authorsRaw)->map(function ($author) {
            return [
                'id' => $author['id'] ?? '',
                'name' => ($author['givenName'][$this->locale] ?? '') . ' ' . ($author['familyName'][$this->locale] ?? ''),
                'email' => $author['email'] ?? null,
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

    public function getCitationsAttribute()
    {
        $citations = json_decode($this->attributes['citations'], true);
        return $citations;
    }

    public function reviewers()
    {
        return $this->belongsToMany(Reviewer::class, 'submission_reviewer');
    }

    public function editors()
    {
        return $this->belongsToMany(Editor::class, 'submission_editor');
    }
}
