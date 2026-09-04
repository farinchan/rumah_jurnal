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
        $raw = $this->attributes['fullTitle'] ?? null;
        if (is_null($raw)) return '';
        $fullTitleRaw = is_string($raw) ? json_decode($raw, true) : $raw;
        if (is_string($fullTitleRaw)) {
            $fullTitleRaw = json_decode($fullTitleRaw, true) ?? $fullTitleRaw;
        }

        if (is_array($fullTitleRaw)) {
            return $fullTitleRaw[$this->attributes['locale'] ?? 'en'] ?? ($fullTitleRaw['en'] ?? (reset($fullTitleRaw) ?: ''));
        }

        return (string) $fullTitleRaw;
    }

    public function getAuthorsAttribute()
    {
        $raw = $this->attributes['authors'] ?? [];
        $authorsRaw = is_string($raw) ? json_decode($raw, true) : $raw;
        if (is_string($authorsRaw)) {
            $authorsRaw = json_decode($authorsRaw, true);
        }
        if (!is_array($authorsRaw)) {
            $authorsRaw = [];
        }

        $locale = $this->attributes['locale'] ?? 'en';

        $filtered = collect($authorsRaw)->map(function ($author) use ($locale) {
            $name = $author['name'] ?? ($author['fullName'] ?? ((($author['givenName'][$locale] ?? '') . ' ' . ($author['familyName'][$locale] ?? '')) ?: ''));
            $affiliation = null;
            if (isset($author['affiliation'])) {
                $affiliation = is_array($author['affiliation']) ? ($author['affiliation'][$locale] ?? reset($author['affiliation'])) : $author['affiliation'];
            } elseif (isset($author['affiliations'][0]['name'])) {
                $affiliation = is_array($author['affiliations'][0]['name']) ? ($author['affiliations'][0]['name'][$locale] ?? reset($author['affiliations'][0]['name'])) : $author['affiliations'][0]['name'];
            }

            return [
                'id' => $author['id'] ?? '',
                'name' => trim((string) $name),
                'email' => $author['email'] ?? null,
                'affiliation' => $affiliation,
            ];
        });
        return $filtered->values()->all();
    }

    public function getAbstractAttribute()
    {
        $raw = $this->attributes['abstract'] ?? null;
        if (is_null($raw)) return '';
        $abstract = is_string($raw) ? json_decode($raw, true) : $raw;
        if (is_string($abstract)) {
            $abstract = json_decode($abstract, true) ?? $abstract;
        }

        if (is_array($abstract)) {
            return $abstract[$this->attributes['locale'] ?? 'en'] ?? ($abstract['en'] ?? (reset($abstract) ?: ''));
        }

        return (string) $abstract;
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
