<?php

use App\Mail\ManuscriptSubmissionStatusMail;
use App\Models\Journal;
use App\Models\SettingWebsite;
use App\Models\User;
use App\Models\WaitingSubmission;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['auth.two_factor.enabled' => false]);
    config(['mail.environment' => 'production']);
    Mail::fake();

    $this->whatsappService = Mockery::mock(WhatsappService::class);
    $this->whatsappService
        ->shouldReceive('sendMessage')
        ->byDefault()
        ->andReturn(['success' => true]);
    app()->instance(WhatsappService::class, $this->whatsappService);

    SettingWebsite::create(['name' => 'Rumah Jurnal Test']);

    $this->journal = Journal::create([
        'name' => 'Target Journal',
        'context_id' => 1,
        'url' => 'https://target-journal.test',
        'url_path' => 'target-journal',
        'title' => 'Target Journal',
        'api_key' => 'target-key',
        'ojs_version' => '3.4',
        'last_sync' => now(),
        'type' => 'journal',
    ]);

    $this->otherJournal = Journal::create([
        'name' => 'Other Journal',
        'context_id' => 2,
        'url' => 'https://other-journal.test',
        'url_path' => 'other-journal',
        'title' => 'Other Journal',
        'api_key' => 'other-key',
        'ojs_version' => '3.4',
        'last_sync' => now(),
        'type' => 'journal',
    ]);

    $editorRole = Role::create(['name' => 'editor']);
    $targetPermission = Permission::create(['name' => $this->journal->url_path]);
    Permission::create(['name' => $this->otherJournal->url_path]);

    $this->editor = User::create([
        'name' => 'Journal Editor',
        'email' => 'editor@example.com',
        'phone' => '081234567890',
        'password' => 'password',
    ]);
    $this->editor->assignRole($editorRole);
    $this->editor->givePermissionTo($targetPermission);
});

function createBackWaitingSubmission(Journal $journal, array $overrides = []): WaitingSubmission
{
    return WaitingSubmission::create(array_replace([
        'submission_code' => (string) Str::uuid(),
        'first_name' => 'Author',
        'last_name' => 'Name',
        'email' => Str::uuid().'@example.com',
        'username' => 'author_'.Str::lower(Str::random(10)),
        'password' => 'password',
        'whatsapp_number' => '+6281234567890',
        'institution' => 'Test University',
        'country' => 'Indonesia',
        'target_journal_id' => $journal->id,
        'article_type' => 'research_article',
        'article_language' => 'English',
        'article_title' => 'A Manuscript Requiring Editorial Review',
        'abstract' => str_repeat('Abstract content. ', 10),
        'keywords' => ['research', 'journal', 'article'],
        'reference_list' => 'Reference one.',
        'has_international_authors' => false,
        'international_author_confirmation' => false,
        'is_original_work' => true,
        'not_previously_published' => true,
        'not_under_consideration' => true,
        'all_authors_approved' => true,
        'authorship_information_correct' => true,
        'international_authors_agreed' => true,
        'uses_official_template' => true,
        'agrees_peer_review' => true,
        'agrees_publication_process' => true,
        'agrees_publication_fees' => true,
        'status' => 'waiting',
        'submitted_at' => now(),
    ], $overrides));
}

it('shows only submissions belonging to an authorized journal', function () {
    createBackWaitingSubmission($this->journal, [
        'article_title' => 'Visible Target Manuscript',
    ]);
    createBackWaitingSubmission($this->otherJournal, [
        'article_title' => 'Hidden Other Manuscript',
    ]);

    $this->actingAs($this->editor)
        ->get(route('back.journal.manuscript-submissions.index', $this->journal->url_path))
        ->assertOk()
        ->assertSee('Visible Target Manuscript')
        ->assertDontSee('Hidden Other Manuscript')
        ->assertSee('Manuscript Submissions');
});

it('shows separate waiting and under review badges on the journal page', function () {
    createBackWaitingSubmission($this->journal, [
        'status' => 'waiting',
    ]);
    createBackWaitingSubmission($this->journal, [
        'status' => 'under_review',
    ]);
    createBackWaitingSubmission($this->otherJournal, [
        'status' => 'waiting',
    ]);

    $this->actingAs($this->editor)
        ->get(route('back.journal.index', $this->journal->url_path))
        ->assertOk()
        ->assertSee('Waiting: 1')
        ->assertSee('Under Review: 1')
        ->assertDontSee('Waiting: 2');
});

it('forbids an editor without permission for the requested journal', function () {
    $this->actingAs($this->editor)
        ->get(route('back.journal.manuscript-submissions.index', $this->otherJournal->url_path))
        ->assertForbidden();

    $this->actingAs($this->editor)
        ->get(route('back.journal.index', $this->otherJournal->url_path))
        ->assertForbidden();
});

it('does not expose a submission through another journal URL', function () {
    $submission = createBackWaitingSubmission($this->otherJournal);

    $this->actingAs($this->editor)
        ->get(route('back.journal.manuscript-submissions.show', [
            $this->journal->url_path,
            $submission->submission_code,
        ]))
        ->assertNotFound();
});

it('requires a reason when an editor rejects a submission', function () {
    $submission = createBackWaitingSubmission($this->journal);

    $this->actingAs($this->editor)
        ->patch(route('back.journal.manuscript-submissions.status', [
            $this->journal->url_path,
            $submission->submission_code,
        ]), [
            'status' => 'rejected',
        ])
        ->assertSessionHasErrors('rejection_reason');

    expect($submission->fresh()->status)->toBe('waiting');
    Mail::assertNothingSent();
});

it('updates review audit and notifies the author by email and WhatsApp', function () {
    $submission = createBackWaitingSubmission($this->journal);

    $this->actingAs($this->editor)
        ->from(route('back.journal.manuscript-submissions.show', [
            $this->journal->url_path,
            $submission->submission_code,
        ]))
        ->patch(route('back.journal.manuscript-submissions.status', [
            $this->journal->url_path,
            $submission->submission_code,
        ]), [
            'status' => 'rejected',
            'editor_notes' => 'Internal screening note.',
            'rejection_reason' => 'The manuscript is outside the journal scope.',
        ])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    $submission->refresh();

    expect($submission->status)->toBe('rejected')
        ->and($submission->reviewed_by)->toBe($this->editor->id)
        ->and($submission->reviewed_at)->not->toBeNull()
        ->and($submission->editor_notes)->toBe('Internal screening note.')
        ->and($submission->rejection_reason)->toBe('The manuscript is outside the journal scope.');

    Mail::assertSent(ManuscriptSubmissionStatusMail::class, function ($mail) use ($submission) {
        return $mail->hasTo($submission->email)
            && $mail->submission->is($submission);
    });

    $this->whatsappService
        ->shouldHaveReceived('sendMessage')
        ->with($submission->whatsapp_number, Mockery::on(
            fn (string $message) => str_contains($message, 'Tidak Dilanjutkan')
                && str_contains($message, $submission->rejection_reason)
        ))
        ->once();
});
