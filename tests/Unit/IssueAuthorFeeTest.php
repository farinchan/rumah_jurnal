<?php

use App\Models\Journal;
use App\Models\Issue;

uses(Tests\TestCase::class, Illuminate\Foundation\Testing\RefreshDatabase::class);

it('automatically copies author_fee from journal when creating a new issue', function () {
    $journal = Journal::create([
        'name' => 'Journal of Science',
        'context_id' => 101,
        'url' => 'https://journal.test',
        'url_path' => 'jsci',
        'title' => 'Journal of Science Title',
        'author_fee' => 1500000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = $journal->issues()->create([
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Volume 1 Issue 1',
    ]);

    expect($issue->author_fee)->toBe(1500000);

    // Updating journal author_fee later should NOT affect existing issue author_fee
    $journal->update(['author_fee' => 2000000]);

    expect($issue->fresh()->author_fee)->toBe(1500000)
        ->and($journal->fresh()->author_fee)->toBe(2000000);
});

it('can run issues:sync-author-fee command to update issue fees', function () {
    $journal = Journal::create([
        'name' => 'Journal of Arts',
        'context_id' => 102,
        'url' => 'https://arts.test',
        'url_path' => 'jarts',
        'title' => 'Journal of Arts Title',
        'author_fee' => 1200000,
        'api_key' => 'secret_key_arts',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    // Manually force an issue with 0 author_fee
    $issue = Issue::withoutEvents(function () use ($journal) {
        return Issue::create([
            'journal_id' => $journal->id,
            'volume' => '2',
            'number' => '1',
            'year' => '2026',
            'title' => 'Volume 2 Issue 1',
            'author_fee' => 0,
        ]);
    });

    expect($issue->author_fee)->toBe(0);

    $this->artisan('issues:sync-author-fee')
        ->assertExitCode(0);

    expect($issue->fresh()->author_fee)->toBe(1200000);
});
