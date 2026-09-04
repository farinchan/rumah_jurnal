<?php

use App\Models\Journal;
use App\Models\Issue;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('executes sync-issue-author-fees route successfully', function () {
    $journal = Journal::create([
        'name' => 'Journal of Tech',
        'context_id' => 201,
        'url' => 'https://tech.test',
        'url_path' => 'jtech',
        'title' => 'Journal of Tech Title',
        'author_fee' => 750000,
        'api_key' => 'secret_key_tech',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::withoutEvents(function () use ($journal) {
        return Issue::create([
            'journal_id' => $journal->id,
            'volume' => '1',
            'number' => '2',
            'year' => '2026',
            'title' => 'Volume 1 Issue 2',
            'author_fee' => 0,
        ]);
    });

    $response = $this->get(route('sync-issue-author-fees'));

    $response->assertStatus(200)
        ->assertJson([
            'status' => true,
            'exit_code' => 0,
        ]);

    expect($issue->fresh()->author_fee)->toBe(750000);
});
