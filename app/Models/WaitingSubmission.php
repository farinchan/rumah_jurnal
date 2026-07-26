<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WaitingSubmission extends Model
{
    protected $fillable = [
        'submission_code',
        'first_name',
        'last_name',
        'email',
        'username',
        'password',
        'whatsapp_number',
        'institution',
        'country',
        'orcid_id',
        'scopus_or_scholar_url',
        'target_journal_id',
        'article_type',
        'article_language',
        'article_title',
        'abstract',
        'keywords',
        'reference_list',
        'correspondence_email',
        'funding_source',
        'has_international_authors',
        'international_authors',
        'international_author_confirmation',
        'is_original_work',
        'not_previously_published',
        'not_under_consideration',
        'all_authors_approved',
        'authorship_information_correct',
        'international_authors_agreed',
        'uses_official_template',
        'agrees_peer_review',
        'agrees_publication_process',
        'agrees_publication_fees',
        'status',
        'submitted_at',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'keywords' => 'array',
            'has_international_authors' => 'boolean',
            'international_authors' => 'array',
            'international_author_confirmation' => 'boolean',
            'is_original_work' => 'boolean',
            'not_previously_published' => 'boolean',
            'not_under_consideration' => 'boolean',
            'all_authors_approved' => 'boolean',
            'authorship_information_correct' => 'boolean',
            'international_authors_agreed' => 'boolean',
            'uses_official_template' => 'boolean',
            'agrees_peer_review' => 'boolean',
            'agrees_publication_process' => 'boolean',
            'agrees_publication_fees' => 'boolean',
            'submitted_at' => 'datetime',
        ];
    }

    public function targetJournal(): BelongsTo
    {
        return $this->belongsTo(Journal::class, 'target_journal_id');
    }
}
