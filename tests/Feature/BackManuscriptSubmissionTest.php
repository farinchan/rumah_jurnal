<?php

use App\Mail\ManuscriptSubmissionStatusMail;
use App\Models\Journal;
use App\Models\SettingWebsite;
use App\Models\User;
use App\Models\WaitingSubmission;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
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

it('creates an OJS account from the submitted author fields when accepted', function () {
    Http::fake([
        'https://target-journal.test/api/v1/users' => Http::response([
            'id' => 3,
            'username' => 'budi',
            'email' => 'budi@example.com',
            'givenName' => 'Budi',
            'familyName' => 'Santoso',
            'phone' => '+62 812-3456-7890',
            'userGroupIds' => [17, 14],
        ], 201),
    ]);

    $submission = createBackWaitingSubmission($this->journal, [
        'first_name' => 'Budi',
        'last_name' => 'Santoso',
        'email' => 'budi@example.com',
        'username' => 'budi',
        'password' => Crypt::encryptString('Rahasia123!'),
        'whatsapp_number' => '+62 812-3456-7890',
        'institution' => 'Universitas Contoh',
        'country' => 'Indonesia',
        'status' => 'under_review',
    ]);

    $statusRoute = route('back.journal.manuscript-submissions.status', [
        $this->journal->url_path,
        $submission->submission_code,
    ]);

    $this->actingAs($this->editor)
        ->patch($statusRoute, ['status' => 'accepted'])
        ->assertSessionHasNoErrors()
        ->assertRedirect();

    Http::assertSent(function ($request) {
        return $request->url() === 'https://target-journal.test/api/v1/users'
            && $request->method() === 'POST'
            && $request->hasHeader('Authorization', 'Bearer target-key')
            && $request->data() === [
                'username' => 'budi',
                'password' => 'Rahasia123!',
                'email' => 'budi@example.com',
                'givenName' => 'Budi',
                'familyName' => 'Santoso',
                'locale' => 'id_ID',
                'country' => 'ID',
                'phone' => '+62 812-3456-7890',
                'affiliation' => 'Universitas Contoh',
                'mustChangePassword' => true,
            ];
    });

    $submission->refresh();

    expect($submission->status)->toBe('accepted')
        ->and($submission->ojs_user_id)->toBe('3')
        ->and($submission->ojs_account_created_at)->not->toBeNull()
        ->and($submission->ojs_response)->toBeArray()
        ->and($submission->ojs_response['id'])->toBe(3);

    Mail::assertSent(ManuscriptSubmissionStatusMail::class, function ($mail) use ($submission) {
        return $mail->hasTo($submission->email)
            && $mail->ojsCredentials === [
                'username' => 'budi',
                'password' => 'Rahasia123!',
                'login_url' => 'https://target-journal.test',
            ];
    });

    $this->whatsappService
        ->shouldHaveReceived('sendMessage')
        ->with($submission->whatsapp_number, Mockery::on(
            fn (string $message) => str_contains($message, 'Username: budi')
                && str_contains($message, 'Password sementara: Rahasia123!')
        ))
        ->once();

    $this->actingAs($this->editor)
        ->patch($statusRoute, ['status' => 'accepted'])
        ->assertSessionHasNoErrors();

    Http::assertSentCount(1);
    Mail::assertSent(ManuscriptSubmissionStatusMail::class, 1);
});

it('does not accept the submission when OJS account creation fails', function () {
    Http::fake([
        'https://target-journal.test/api/v1/users' => Http::response([
            'error' => 'username_exists',
            'message' => 'The username is already in use.',
        ], 422),
    ]);

    $submission = createBackWaitingSubmission($this->journal, [
        'password' => Crypt::encryptString('Rahasia123!'),
        'status' => 'under_review',
    ]);

    $this->actingAs($this->editor)
        ->patch(route('back.journal.manuscript-submissions.status', [
            $this->journal->url_path,
            $submission->submission_code,
        ]), [
            'status' => 'accepted',
        ])
        ->assertSessionHasErrors([
            'ojs_account' => 'The username is already in use.',
        ]);

    expect($submission->fresh()->status)->toBe('under_review')
        ->and($submission->fresh()->ojs_account_created_at)->toBeNull();

    Mail::assertNothingSent();
    $this->whatsappService->shouldNotHaveReceived('sendMessage');
});

it('generates a recoverable temporary password for a legacy hashed submission', function () {
    $passwordSentToOjs = null;

    Http::fake(function ($request) use (&$passwordSentToOjs) {
        $passwordSentToOjs = $request['password'];

        return Http::response(['id' => 988], 201);
    });

    $submission = createBackWaitingSubmission($this->journal, [
        'password' => Hash::make('legacy-password'),
        'status' => 'under_review',
    ]);

    $this->actingAs($this->editor)
        ->patch(route('back.journal.manuscript-submissions.status', [
            $this->journal->url_path,
            $submission->submission_code,
        ]), [
            'status' => 'accepted',
        ])
        ->assertSessionHasNoErrors();

    $submission->refresh();

    expect($passwordSentToOjs)
        ->toBeString()
        ->not->toBe('legacy-password')
        ->and(Crypt::decryptString($submission->password))->toBe($passwordSentToOjs);

    Mail::assertSent(ManuscriptSubmissionStatusMail::class, function ($mail) use ($passwordSentToOjs) {
        return $mail->ojsCredentials['password'] === $passwordSentToOjs;
    });
});
