<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Issue extends Model
{
    use  LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
        ->logUnguarded()
        ->logOnlyDirty()
        ->setDescriptionForEvent(fn (string $eventName) => "This model has been {$eventName}");
    }
    protected static function booted(): void
    {
        static::creating(function (Issue $issue) {
            if (is_null($issue->author_fee) || $issue->author_fee === '') {
                $journal = $issue->journal ?? ($issue->journal_id ? Journal::find($issue->journal_id) : null);
                $issue->author_fee = $journal?->author_fee ?? 0;
            }
            if (!array_key_exists('max_articles', $issue->getAttributes())) {
                $issue->max_articles = 10;
            } elseif ($issue->max_articles === '') {
                $issue->max_articles = null;
            }
            if ($issue->month === '' || $issue->month === null) {
                $issue->month = null;
            } else {
                $issue->month = (int) $issue->month;
            }
        });

        static::updating(function (Issue $issue) {
            if ($issue->month === '' || $issue->month === null) {
                $issue->month = null;
            } else {
                $issue->month = (int) $issue->month;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'author_fee' => 'integer',
            'max_articles' => 'integer',
            'month' => 'integer',
        ];
    }

    public function getPublicationMonthAttribute(): ?int
    {
        return $this->month ? (int) $this->month : null;
    }

    public function setPublicationMonthAttribute($value): void
    {
        $this->attributes['month'] = ($value === '' || $value === null) ? null : (int) $value;
    }

    public function getMonthNameAttribute(): ?string
    {
        if (!$this->month) {
            return null;
        }

        $months = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $months[(int) $this->month] ?? null;
    }

    protected $guarded = ['id', 'created_at', 'updated_at'];

    public function journal()
    {
        return $this->belongsTo(Journal::class, 'journal_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'issue_id');
    }

    public function editors()
    {
        return $this->hasMany(Editor::class, 'issue_id');
    }

    public function reviewers()
    {
        return $this->hasMany(Reviewer::class, 'issue_id');
    }
}
