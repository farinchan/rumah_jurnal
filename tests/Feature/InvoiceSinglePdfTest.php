<?php

use App\Models\Issue;
use App\Models\Journal;
use App\Models\PaymentAccount;
use App\Models\PaymentInvoice;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    config(['auth.two_factor.enabled' => false]);
    SettingWebsite::create(['name' => 'Rumah Jurnal Test']);

    PaymentAccount::create([
        'bank' => 'Bank Syariah Indonesia (BSI)',
        'account_number' => '1234567890',
        'account_name' => 'Rumah Jurnal UIN Bukittinggi',
    ]);

    $role = Role::create(['name' => 'super-admin']);
    $this->user = User::factory()->create([
        'email' => 'admin@example.com',
    ]);
    $this->user->assignRole('super-admin');

    $this->journal = Journal::create([
        'name' => 'Journal of Islamic Studies',
        'context_id' => 1,
        'url' => 'https://journal.test',
        'url_path' => 'jis',
        'title' => 'Journal of Islamic Studies',
        'api_key' => 'test-key',
        'ojs_version' => '3.4',
        'last_sync' => now(),
        'type' => 'journal',
        'author_fee' => 1000000,
    ]);

    $this->issue = Issue::create([
        'journal_id' => $this->journal->id,
        'volume' => '10',
        'number' => '1',
        'year' => '2026',
        'title' => 'Regular Issue',
        'author_fee' => 1000000,
    ]);

    $this->submission = Submission::create([
        'submission_id' => 'SUB-999',
        'issue_id' => $this->issue->id,
        'locale' => 'en',
        'lastModified' => now(),
        'fullTitle' => json_encode(['en' => 'Collaborative Research on Islamic Finance']),
        'authors' => json_encode([
            [
                'id' => 1,
                'fullName' => 'First Author',
                'email' => 'author1@test.com',
                'affiliation' => ['en' => 'University A'],
            ],
            [
                'id' => 2,
                'fullName' => 'Second Author',
                'email' => 'author2@test.com',
                'affiliation' => ['en' => 'University B'],
            ]
        ]),
        'authorsString' => 'First Author, Second Author',
    ]);
});

it('downloads a single PDF for invoice 60 percent instead of a zip file', function () {
    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.generate1', $this->submission->id));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename=invoice-SUB-999-60.pdf');
});

it('downloads a single PDF for invoice 40 percent instead of a zip file', function () {
    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.generate2', $this->submission->id));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename=invoice-SUB-999-40.pdf');
});

it('downloads a single PDF for invoice 100 percent instead of a zip file', function () {
    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.generate3', $this->submission->id));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename=invoice-SUB-999-100.pdf');
});

it('downloads a single PDF for custom invoice instead of a zip file', function () {
    $customInvoice = PaymentInvoice::create([
        'invoice_number' => '0555',
        'payment_percent' => null,
        'payment_amount' => 750000,
        'payment_due_date' => now()->addDays(3),
        'submission_id' => $this->submission->id,
        'is_custom' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.custom.generate', $customInvoice->id));

    $response->assertOk();
    $response->assertHeader('content-disposition', 'attachment; filename=invoice-SUB-999-custom-' . $customInvoice->id . '.pdf');
});

it('sends invoice 60 percent mail to first author only with single PDF attachment', function () {
    \Illuminate\Support\Facades\Mail::fake();
    config(['mail.environment' => 'production']);

    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.mail-send1', $this->submission->id));

    $response->assertRedirect();
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, function ($mail) {
        return $mail->hasTo('author1@test.com') && !$mail->hasTo('author2@test.com');
    });
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, 1);
});

it('sends invoice 40 percent mail to first author only with single PDF attachment', function () {
    \Illuminate\Support\Facades\Mail::fake();
    config(['mail.environment' => 'production']);

    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.mail-send2', $this->submission->id));

    $response->assertRedirect();
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, function ($mail) {
        return $mail->hasTo('author1@test.com') && !$mail->hasTo('author2@test.com');
    });
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, 1);
});

it('sends invoice 100 percent mail to first author only with single PDF attachment', function () {
    \Illuminate\Support\Facades\Mail::fake();
    config(['mail.environment' => 'production']);

    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.mail-send3', $this->submission->id));

    $response->assertRedirect();
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, function ($mail) {
        return $mail->hasTo('author1@test.com') && !$mail->hasTo('author2@test.com');
    });
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, 1);
});

it('sends custom invoice mail to first author only with single PDF attachment', function () {
    \Illuminate\Support\Facades\Mail::fake();
    config(['mail.environment' => 'production']);

    $customInvoice = PaymentInvoice::create([
        'invoice_number' => '0556',
        'payment_percent' => null,
        'payment_amount' => 500000,
        'payment_due_date' => now()->addDays(3),
        'submission_id' => $this->submission->id,
        'is_custom' => true,
    ]);

    $response = $this->actingAs($this->user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.journal.invoice.custom.mail-send', $customInvoice->id));

    $response->assertRedirect();
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, function ($mail) {
        return $mail->hasTo('author1@test.com') && !$mail->hasTo('author2@test.com');
    });
    \Illuminate\Support\Facades\Mail::assertSent(\App\Mail\InvoiceMail::class, 1);
});

