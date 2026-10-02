<?php

use App\Models\Journal;
use App\Models\Issue;
use App\Models\SettingWebsite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin']);
    Role::firstOrCreate(['name' => 'admin-ejournal']);
    Role::firstOrCreate(['name' => 'admin-proceeding']);
    Role::firstOrCreate(['name' => 'admin-student-research-hub']);
    Role::firstOrCreate(['name' => 'editor']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

if (!function_exists('makeIssueTestJournal')) {
    function makeIssueTestJournal(string $path = 'jtest'): Journal
    {
        Permission::firstOrCreate(['name' => $path]);

        return Journal::create([
            'name' => 'Journal Test',
            'title' => 'Journal Test Title',
            'context_id' => 100,
            'url' => 'https://journal.test/' . $path,
            'url_path' => $path,
            'type' => 'journal',
            'author_fee' => 1000000,
            'api_key' => 'secret_key',
            'ojs_version' => '3.3',
            'last_sync' => now(),
        ]);
    }
}

it('defaults max_articles to 10 when creating an issue without specifying it', function () {
    $journal = makeIssueTestJournal();

    $issue = $journal->issues()->create([
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Issue 1',
    ]);

    expect($issue->fresh()->max_articles)->toBe(10);
});

it('allows max_articles to be explicitly set to null (tanpa batas)', function () {
    $journal = makeIssueTestJournal('jnull');

    $issue = $journal->issues()->create([
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Issue Unlimited',
        'max_articles' => null,
    ]);

    expect($issue->fresh()->max_articles)->toBeNull();
});

it('allows super-admin to set, update, and clear max_articles to null', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = makeIssueTestJournal('jsuper');

    // Create issue with max_articles = 25
    $response = $this->actingAs($user)->post(route('back.journal.issue.store', $journal->url_path), [
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Issue Super Admin',
        'max_articles' => 25,
    ]);

    $response->assertRedirect();
    $issue = Issue::where('journal_id', $journal->id)->first();
    expect($issue)->not->toBeNull()
        ->and($issue->max_articles)->toBe(25);

    // Update issue to max_articles = null (empty string / tanpa batas)
    $updateResponse = $this->actingAs($user)->put(route('back.journal.issue.update', [$journal->url_path, $issue->id]), [
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Issue Super Admin Updated',
        'max_articles' => '',
    ]);

    $updateResponse->assertRedirect();
    expect($issue->fresh()->max_articles)->toBeNull();
});

it('allows admin-ejournal, admin-proceeding, and admin-student-research-hub to set and update max_articles', function (string $role) {
    $user = User::factory()->create();
    $user->assignRole($role);

    $journal = makeIssueTestJournal('j-' . $role);
    $user->givePermissionTo($journal->url_path);

    // Store with custom max_articles
    $response = $this->actingAs($user)->post(route('back.journal.issue.store', $journal->url_path), [
        'volume' => '2',
        'number' => '1',
        'year' => '2026',
        'title' => 'Issue by ' . $role,
        'max_articles' => 20,
    ]);

    $response->assertRedirect();
    $issue = Issue::where('journal_id', $journal->id)->first();
    expect($issue)->not->toBeNull()
        ->and($issue->max_articles)->toBe(20);

    // Update with empty max_articles (cleared to null = unlimited)
    $updateResponse = $this->actingAs($user)->put(route('back.journal.issue.update', [$journal->url_path, $issue->id]), [
        'volume' => '2',
        'number' => '1',
        'year' => '2026',
        'title' => 'Issue updated by ' . $role,
        'max_articles' => '',
    ]);

    $updateResponse->assertRedirect();
    expect($issue->fresh()->max_articles)->toBeNull();
})->with([
    'admin-ejournal',
    'admin-proceeding',
    'admin-student-research-hub',
]);

it('does not allow editor to set max_articles on create and falls back to default 10', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $journal = makeIssueTestJournal('jeditor-create');
    $editor->givePermissionTo($journal->url_path);

    // Editor tries to send max_articles = 50 or empty
    $response = $this->actingAs($editor)->post(route('back.journal.issue.store', $journal->url_path), [
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Issue by Editor',
        'max_articles' => 50,
    ]);

    $response->assertRedirect();
    $issue = Issue::where('journal_id', $journal->id)->first();
    expect($issue)->not->toBeNull()
        ->and($issue->max_articles)->toBe(10);
});

it('does not allow editor to update max_articles and preserves original value', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $journal = makeIssueTestJournal('jeditor-update');
    $editor->givePermissionTo($journal->url_path);

    // Initial issue has max_articles = null (unlimited)
    $issue = Issue::withoutEvents(function () use ($journal) {
        return Issue::create([
            'journal_id' => $journal->id,
            'volume' => '1',
            'number' => '1',
            'year' => '2026',
            'title' => 'Initial Issue',
            'max_articles' => null,
            'author_fee' => 0,
        ]);
    });

    // Editor tries to update max_articles = 99
    $response = $this->actingAs($editor)->put(route('back.journal.issue.update', [$journal->url_path, $issue->id]), [
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Updated Issue Title',
        'max_articles' => 99,
    ]);

    $response->assertRedirect();
    expect($issue->fresh()->max_articles)->toBeNull()
        ->and($issue->fresh()->title)->toBe('Updated Issue Title');
});

it('shows max_articles input for allowed roles and read-only for editor on detail setting page', function () {
    $journal = makeIssueTestJournal('jviews');

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'View Issue',
        'max_articles' => 12,
    ]);

    // Admin sees editable input
    $admin = User::factory()->create();
    $admin->assignRole('admin-ejournal');
    $admin->givePermissionTo($journal->url_path);

    $adminResponse = $this->actingAs($admin)->get(route('back.journal.setting.index', [$journal->url_path, $issue->id]));
    $adminResponse->assertOk();
    $adminResponse->assertSee('name="max_articles"', false);
    $adminResponse->assertSee('value="12"', false);

    // Editor sees disabled input without name attribute
    $editor = User::factory()->create();
    $editor->assignRole('editor');
    $editor->givePermissionTo($journal->url_path);

    $editorResponse = $this->actingAs($editor)->get(route('back.journal.setting.index', [$journal->url_path, $issue->id]));
    $editorResponse->assertOk();
    $editorResponse->assertDontSee('name="max_articles"', false);
    $editorResponse->assertSee('disabled readonly', false);
    $editorResponse->assertSee('value="12"', false);
});

it('shows infinity symbol and tidak ada batas when max_articles is null', function () {
    $journal = makeIssueTestJournal('junlimited');

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Unlimited Issue',
        'max_articles' => null,
    ]);

    $editor = User::factory()->create();
    $editor->assignRole('editor');
    $editor->givePermissionTo($journal->url_path);

    // Setting page shows "Tidak Ada Batas" for editor
    $response = $this->actingAs($editor)->get(route('back.journal.setting.index', [$journal->url_path, $issue->id]));
    $response->assertOk();
    $response->assertSee('value="Tidak Ada Batas"', false);

    // Journal index shows ∞ symbol
    $indexResponse = $this->actingAs($editor)->get(route('back.journal.index', $journal->url_path));
    $indexResponse->assertOk();
    $indexResponse->assertSee('∞', false);
    $indexResponse->assertSee('Tanpa Batas', false);
});

it('hides the tambah artikel button and modal when max_articles is reached', function () {
    $journal = makeIssueTestJournal('jfull');

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Full Issue',
        'max_articles' => 2,
    ]);

    // Create 2 submissions for this issue
    for ($i = 1; $i <= 2; $i++) {
        \App\Models\Submission::create([
            'issue_id' => $issue->id,
            'submission_id' => 'SUB-' . $i,
            'fullTitle' => 'Article ' . $i,
            'authorsString' => 'Author ' . $i,
            'status' => '3',
            'status_label' => 'Published',
            'lastModified' => now()->toDateTimeString(),
        ]);
    }

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->actingAs($user)->get(route('back.journal.article.index', [$journal->url_path, $issue->id]));
    $response->assertOk();
    $response->assertDontSee('id="btn_add_article"', false);
    $response->assertDontSee('data-bs-target="#modal_select_article"', false);
    $response->assertSee('Maksimal Artikel Tercapai (2/2)', false);
});

it('shows the tambah artikel button when max_articles is not reached or null', function () {
    $journal = makeIssueTestJournal('jnotfull');

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Not Full Issue',
        'max_articles' => 2,
    ]);

    // Create only 1 submission (1 < 2)
    \App\Models\Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-1',
        'fullTitle' => 'Article 1',
        'authorsString' => 'Author 1',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->actingAs($user)->get(route('back.journal.article.index', [$journal->url_path, $issue->id]));
    $response->assertOk();
    $response->assertSee('id="btn_add_article"', false);
    $response->assertSee('Tambah Artikel', false);
});

it('rejects adding new submission via api when max_articles is reached', function () {
    $journal = makeIssueTestJournal('japi-full');

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'API Full Issue',
        'max_articles' => 1,
    ]);

    \App\Models\Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-EXISTING',
        'fullTitle' => 'Existing Article',
        'authorsString' => 'Author',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->actingAs($user)->postJson(route('api.v1.submissions.select'), [
        'jurnal_path' => $journal->url_path,
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-NEW-99',
    ]);

    $response->assertStatus(422);
    $response->assertJson([
        'success' => false,
        'error' => 'Batas maksimal artikel telah tercapai',
    ]);
});

it('rejects moving submission to a full issue via articleMoveIssue', function () {
    $journal = makeIssueTestJournal('jmove');

    $sourceIssue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2026',
        'title' => 'Source Issue',
        'max_articles' => 10,
    ]);

    $targetIssue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '2',
        'year' => '2026',
        'title' => 'Target Issue Full',
        'max_articles' => 1,
    ]);

    // Target issue already has 1 article (full)
    \App\Models\Submission::create([
        'issue_id' => $targetIssue->id,
        'submission_id' => 'SUB-IN-TARGET',
        'fullTitle' => 'Target Article',
        'authorsString' => 'Author Target',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    // Source issue submission to move
    $subToMove = \App\Models\Submission::create([
        'issue_id' => $sourceIssue->id,
        'submission_id' => 'SUB-TO-MOVE',
        'fullTitle' => 'Article to move',
        'authorsString' => 'Author Move',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $response = $this->actingAs($user)->put(route('back.journal.article.move-issue', [
        $journal->url_path,
        $sourceIssue->id,
        $subToMove->id,
    ]), [
        'target_issue_id' => $targetIssue->id,
    ]);

    $response->assertRedirect();
    // Verify submission is still in source issue, not moved
    expect($subToMove->fresh()->issue_id)->toBe($sourceIssue->id);
});
