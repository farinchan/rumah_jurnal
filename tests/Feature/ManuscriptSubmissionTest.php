<?php

use App\Mail\ManuscriptSubmissionReceivedMail;
use App\Mail\NewManuscriptSubmissionEditorMail;
use App\Models\Journal;
use App\Models\SettingWebsite;
use App\Models\User;
use App\Models\WaitingSubmission;
use App\Services\WhatsappService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['mail.environment' => 'production']);
    Mail::fake();
    $this->whatsappService = Mockery::mock(WhatsappService::class);
    $this->whatsappService
        ->shouldReceive('sendMessage')
        ->byDefault()
        ->andReturn(['success' => true, 'message' => 'Message sent successfully']);
    app()->instance(WhatsappService::class, $this->whatsappService);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
    ]);

    Journal::create([
        'name' => 'Test Journal',
        'context_id' => 1,
        'url' => 'https://journal.test',
        'url_path' => 'test-journal',
        'title' => 'Test Journal',
        'api_key' => 'test-api-key',
        'ojs_version' => '3.4',
        'last_sync' => now(),
        'type' => 'journal',
    ]);
});

function validManuscriptSubmissionData(array $overrides = []): array
{
    return array_replace([
        'first_name' => 'Fajri',
        'last_name' => 'Rinaldi Chan',
        'email' => 'fajri@gariskode.com',
        'username' => 'fajri_chan',
        'password' => 'password',
        'password_confirmation' => 'password',
        'whatsapp_number' => '+6281234567890',
        'institution' => 'UIN Test',
        'country' => 'Indonesia',
        'orcid_id' => '0000-0002-1825-0097',
        'scopus_or_scholar_url' => 'https://scholar.google.com/example',
        'target_journal_id' => Journal::first()->id,
        'article_type' => 'research_article',
        'article_language' => 'English',
        'article_title' => 'A Valid Research Article Title',
        'abstract' => str_repeat('This is a sufficiently detailed abstract for manuscript validation. ', 3),
        'keywords' => ['journal', 'research', 'publication'],
        'reference_list' => "Reference one.\nReference two.",
        'has_international_authors' => '0',
        'is_original_work' => '1',
        'not_previously_published' => '1',
        'not_under_consideration' => '1',
        'all_authors_approved' => '1',
        'authorship_information_correct' => '1',
        'international_authors_agreed' => '1',
        'uses_official_template' => '1',
        'agrees_peer_review' => '1',
        'agrees_publication_process' => '1',
        'agrees_publication_fees' => '1',
    ], $overrides);
}

it('shows the manuscript submission form', function () {
    $this->get(route('manuscript-submission.create'))
        ->assertOk()
        ->assertSee('Submit Your Manuscript')
        ->assertSee('Test Journal')
        ->assertSee('select2@4.0.13', false)
        ->assertSee('Select or search the intended UIN journal');
});

it('stores a valid manuscript submission with an encrypted password', function () {
    $response = $this->post(route('manuscript-submission.store'), validManuscriptSubmissionData());

    $response->assertSessionHasNoErrors();

    $submission = WaitingSubmission::first();

    expect($submission)
        ->not->toBeNull()
        ->and($submission->status)->toBe('waiting')
        ->and($submission->keywords)->toBe(['journal', 'research', 'publication'])
        ->and($submission->has_international_authors)->toBeFalse()
        ->and($submission->password)->not->toBe('password')
        ->and(Crypt::decryptString($submission->password))->toBe('password');

    Mail::assertSent(ManuscriptSubmissionReceivedMail::class, function ($mail) use ($submission) {
        return $mail->hasTo('fajri@gariskode.com')
            && $mail->submission->is($submission);
    });

    $this->whatsappService
        ->shouldHaveReceived('sendMessage')
        ->once()
        ->with($submission->whatsapp_number, Mockery::on(
            fn (string $message) => str_contains($message, $submission->submission_code)
                && str_contains($message, $submission->article_title)
        ));

    $response->assertRedirect(route('manuscript-submission.success', $submission->submission_code));
});

it('redirects manuscript email to the local address outside production', function () {
    config([
        'mail.environment' => 'local',
        'mail.local_address' => 'local-mailbox@example.com',
    ]);

    $this->post(route('manuscript-submission.store'), validManuscriptSubmissionData([
        'email' => 'local-environment-author@example.com',
        'username' => 'local_environment_author',
    ]))->assertSessionHasNoErrors();

    Mail::assertSent(ManuscriptSubmissionReceivedMail::class, function ($mail) {
        return $mail->hasTo('local-mailbox@example.com')
            && ! $mail->hasTo('local-environment-author@example.com');
    });
});

it('requires three to five keywords and every author declaration', function () {
    $data = validManuscriptSubmissionData([
        'keywords' => ['research', 'journal'],
    ]);
    unset($data['is_original_work']);

    $this->post(route('manuscript-submission.store'), $data)
        ->assertSessionHasErrors(['keywords', 'is_original_work']);

    $this->assertDatabaseCount('waiting_submissions', 0);
    Mail::assertNothingSent();
});

it('stores international author information after consent is confirmed', function () {
    $data = validManuscriptSubmissionData([
        'email' => 'international@example.com',
        'username' => 'international_author',
        'has_international_authors' => '1',
        'international_authors' => [[
            'institution_name' => 'International Test University',
            'department' => 'Faculty of Science',
            'country' => 'Malaysia',
            'institutional_email' => 'author@university.test',
            'orcid_or_scopus_id' => '0000-0001-2345-6789',
            'contribution' => 'Methodology and data analysis.',
            'consent' => '1',
        ]],
        'international_author_confirmation' => '1',
    ]);

    $this->post(route('manuscript-submission.store'), $data)
        ->assertSessionHasNoErrors();

    $submission = WaitingSubmission::where('email', 'international@example.com')->firstOrFail();

    expect($submission->has_international_authors)->toBeTrue()
        ->and($submission->international_author_confirmation)->toBeTrue()
        ->and($submission->international_authors[0]['country'])->toBe('Malaysia');
});

it('notifies only editors with permission for the target journal URL path', function () {
    $editorRole = Role::create(['name' => 'editor']);
    $targetPermission = Permission::create(['name' => 'test-journal']);
    $otherPermission = Permission::create(['name' => 'another-journal']);

    $matchingEditor = User::create([
        'name' => 'Matching Editor',
        'email' => 'matching-editor@example.com',
        'phone' => '081234567801',
        'password' => 'password',
    ]);
    $matchingEditor->assignRole($editorRole);
    $matchingEditor->givePermissionTo($targetPermission);

    $otherEditor = User::create([
        'name' => 'Other Editor',
        'email' => 'other-editor@example.com',
        'phone' => '081234567802',
        'password' => 'password',
    ]);
    $otherEditor->assignRole($editorRole);
    $otherEditor->givePermissionTo($otherPermission);

    $this->post(route('manuscript-submission.store'), validManuscriptSubmissionData([
        'email' => 'editor-notification-author@example.com',
        'username' => 'editor_notification_author',
    ]))->assertSessionHasNoErrors();

    Mail::assertSent(NewManuscriptSubmissionEditorMail::class, function ($mail) use ($matchingEditor) {
        return $mail->hasTo($matchingEditor->email)
            && $mail->editor->is($matchingEditor);
    });

    Mail::assertNotSent(NewManuscriptSubmissionEditorMail::class, function ($mail) use ($otherEditor) {
        return $mail->hasTo($otherEditor->email);
    });

    Mail::assertSent(NewManuscriptSubmissionEditorMail::class, 1);

    $this->whatsappService
        ->shouldHaveReceived('sendMessage')
        ->with($matchingEditor->phone, Mockery::on(
            fn (string $message) => str_contains($message, 'Ada manuscript submission baru')
                && str_contains($message, 'Test Journal')
        ))
        ->once();

    $this->whatsappService
        ->shouldNotHaveReceived('sendMessage', function (string $phone) use ($otherEditor) {
            return $phone === $otherEditor->phone;
        });
});
