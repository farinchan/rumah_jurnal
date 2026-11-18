<?php

use App\Models\Issue;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\PaymentInvoice;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin']);
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'editor']);
    Role::firstOrCreate(['name' => 'keuangan']);
    Role::firstOrCreate(['name' => 'humas']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Security Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

function createSecJournal(string $urlPath): Journal
{
    Permission::firstOrCreate(['name' => $urlPath]);

    return Journal::create([
        'name' => 'Journal ' . $urlPath,
        'title' => 'Title ' . $urlPath,
        'context_id' => rand(100, 999),
        'url' => 'https://journal.test/' . $urlPath,
        'url_path' => $urlPath,
        'type' => 'journal',
        'author_fee' => 1500000,
        'api_key' => 'secret_key_' . $urlPath,
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);
}

function createSecSubmission(int $issueId, string $subId, string $title = 'Title'): Submission
{
    return Submission::create([
        'issue_id' => $issueId,
        'submission_id' => $subId,
        'fullTitle' => ['en' => $title],
        'authorsString' => 'Test Author',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);
}

/*
|--------------------------------------------------------------------------
| SEC-02: Broken Access Control & Role Escalation Protection
|--------------------------------------------------------------------------
*/

it('forbids non-super-admin from accessing user management routes', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $targetUser = User::factory()->create();

    // Editor cannot list users
    $this->actingAs($editor)->get(route('back.master.user.index'))->assertStatus(403);

    // Editor cannot open create form or submit new user (escalation prevention)
    $this->actingAs($editor)->get(route('back.master.user.create'))->assertStatus(403);
    $this->actingAs($editor)->post(route('back.master.user.store'), [
        'name' => 'Hacker Admin',
        'email' => 'hacker@test.com',
        'password' => 'password123',
        'role_super_admin' => '1',
    ])->assertStatus(403);

    // Editor cannot edit or update user
    $this->actingAs($editor)->get(route('back.master.user.edit', $targetUser->id))->assertStatus(403);
    $this->actingAs($editor)->put(route('back.master.user.update', $targetUser->id), [
        'name' => 'Updated By Editor',
    ])->assertStatus(403);

    // Editor cannot delete user
    $this->actingAs($editor)->delete(route('back.master.user.destroy', $targetUser->id))->assertStatus(403);
});

it('allows super-admin to access user management and prevents self-deletion', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');

    // Super-admin can access index
    $this->actingAs($superAdmin)->get(route('back.master.user.index'))->assertStatus(200);

    // Super-admin cannot delete their own account
    $res = $this->actingAs($superAdmin)->delete(route('back.master.user.destroy', $superAdmin->id));
    $res->assertStatus(302);
    expect(User::find($superAdmin->id))->not->toBeNull();

    // Super-admin can delete another user
    $otherUser = User::factory()->create();
    $this->actingAs($superAdmin)->delete(route('back.master.user.destroy', $otherUser->id))->assertStatus(302);
    expect(User::find($otherUser->id))->toBeNull();
});

it('forbids non-super-admin from accessing website settings', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $this->actingAs($editor)->get(route('back.setting.website'))->assertStatus(403);
    $this->actingAs($editor)->put(route('back.setting.website.update'), ['name' => 'Defaced Site'])->assertStatus(403);
    $this->actingAs($editor)->get(route('back.setting.banner'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.setting.bot'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.setting.payment_year_settings'))->assertStatus(403);

    // Super-admin has access
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');
    $this->actingAs($superAdmin)->get(route('back.setting.website'))->assertStatus(200);
});

it('forbids non-finance and non-admin users from accessing finance module', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $this->actingAs($editor)->get(route('back.finance.verification.index'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.finance.report.index'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.finance.cashflow.index'))->assertStatus(403);

    // Keuangan role has access
    $keuangan = User::factory()->create();
    $keuangan->assignRole('keuangan');
    $this->actingAs($keuangan)->get(route('back.finance.verification.index'))->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| SEC-03: Horizontal Broken Access Control & IDOR Protection in Journals
|--------------------------------------------------------------------------
*/

it('forbids editor of Journal A from accessing Journal B resources', function () {
    $journalA = createSecJournal('journal-alpha');
    $journalB = createSecJournal('journal-beta');

    $issueB = Issue::create([
        'journal_id' => $journalB->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Issue Beta 1',
    ]);

    $submissionB = createSecSubmission($issueB->id, 'SUB-B-01', 'Beta Secret Manuscript');

    $editorA = User::factory()->create();
    $editorA->assignRole('editor');
    $editorA->givePermissionTo('journal-alpha');

    // Cannot access Journal B index
    $this->actingAs($editorA)
        ->get(route('back.journal.index', 'journal-beta'))
        ->assertStatus(403);

    // Cannot access Journal B articles
    $this->actingAs($editorA)
        ->get(route('back.journal.article.index', ['journal-beta', $issueB->id]))
        ->assertStatus(403);

    // Cannot query Journal B datatables
    $this->actingAs($editorA)
        ->get(route('back.journal.article.datatable', ['journal-beta', $issueB->id]))
        ->assertStatus(403);

    // Cannot open modal view or action for Journal B
    $this->actingAs($editorA)
        ->get(route('back.journal.article.modal-view', ['journal-beta', $issueB->id, $submissionB->id]))
        ->assertStatus(403);
    $this->actingAs($editorA)
        ->get(route('back.journal.article.modal-action', ['journal-beta', $issueB->id, $submissionB->id]))
        ->assertStatus(403);

    // Cannot update Journal B article
    $this->actingAs($editorA)
        ->put(route('back.journal.article.update', ['journal-beta', $issueB->id, $submissionB->id]), [
            'type' => 'title',
            'title' => 'Hacked Title',
        ])
        ->assertStatus(403);

    // Cannot store issue in Journal B
    $this->actingAs($editorA)
        ->post(route('back.journal.issue.store', 'journal-beta'), [
            'volume' => '9',
            'number' => '9',
            'year' => '2025',
            'title' => 'Unauthorized Issue',
        ])
        ->assertStatus(403);

    // Cannot access editor/reviewer/setting for Journal B
    $this->actingAs($editorA)
        ->get(route('back.journal.editor.index', ['journal-beta', $issueB->id]))
        ->assertStatus(403);
    $this->actingAs($editorA)
        ->get(route('back.journal.reviewer.index', ['journal-beta', $issueB->id]))
        ->assertStatus(403);
    $this->actingAs($editorA)
        ->get(route('back.journal.setting.index', ['journal-beta', $issueB->id]))
        ->assertStatus(403);
});

it('forbids editor of Journal A from generating LoA or Invoices for Journal B', function () {
    $journalA = createSecJournal('journal-alpha-fin');
    $journalB = createSecJournal('journal-beta-fin');

    $issueB = Issue::create([
        'journal_id' => $journalB->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Issue Beta Fin',
    ]);

    $submissionB = createSecSubmission($issueB->id, 'SUB-B-FIN', 'Beta Financial Submission');

    $invoiceB = PaymentInvoice::create([
        'invoice_number' => '9999',
        'payment_percent' => 100,
        'payment_amount' => 1500000,
        'payment_due_date' => now()->addDays(3),
        'submission_id' => $submissionB->id,
    ]);

    $editorA = User::factory()->create();
    $editorA->assignRole('editor');
    $editorA->givePermissionTo('journal-alpha-fin');

    // Generating LoA for submission B is forbidden
    $this->actingAs($editorA)
        ->get(route('back.journal.loa.generate', $submissionB->id))
        ->assertStatus(403);

    // Generating invoice 1, 2, 3 for submission B is forbidden
    $this->actingAs($editorA)
        ->get(route('back.journal.invoice.generate1', $submissionB->id))
        ->assertStatus(403);
    $this->actingAs($editorA)
        ->get(route('back.journal.invoice.generate2', $submissionB->id))
        ->assertStatus(403);
    $this->actingAs($editorA)
        ->get(route('back.journal.invoice.generate3', $submissionB->id))
        ->assertStatus(403);

    // Storing custom invoice for submission B is forbidden
    $this->actingAs($editorA)
        ->post(route('back.journal.invoice.custom.store', $submissionB->id), [
            'custom_amount' => 500000,
        ])
        ->assertStatus(403);

    // Deleting invoice B is forbidden
    $this->actingAs($editorA)
        ->delete(route('back.journal.invoice.destroy', $invoiceB->id))
        ->assertStatus(403);

    expect(PaymentInvoice::find($invoiceB->id))->not->toBeNull();
});

it('allows editor of Journal A to access Journal A properly', function () {
    $journalA = createSecJournal('journal-alpha-ok');

    $issueA = Issue::create([
        'journal_id' => $journalA->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Issue Alpha OK',
    ]);

    $editorA = User::factory()->create();
    $editorA->assignRole('editor');
    $editorA->givePermissionTo('journal-alpha-ok');

    $this->actingAs($editorA)
        ->get(route('back.journal.index', 'journal-alpha-ok'))
        ->assertStatus(200);

    $this->actingAs($editorA)
        ->get(route('back.journal.article.index', ['journal-alpha-ok', $issueA->id]))
        ->assertStatus(200);
});

/*
|--------------------------------------------------------------------------
| SEC-04: HTTP DELETE & CSRF Protection for Payment Deletion
|--------------------------------------------------------------------------
*/

it('rejects payment deletion via HTTP GET with 405 Method Not Allowed', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');

    $journal = createSecJournal('journal-pay-del');
    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Issue Pay Del',
    ]);
    $sub = createSecSubmission($issue->id, 'SUB-PAY-DEL', 'Payment Deletion Article');
    $inv = PaymentInvoice::create([
        'invoice_number' => '8888',
        'payment_percent' => 100,
        'payment_amount' => 1000000,
        'payment_due_date' => now()->addDays(3),
        'submission_id' => $sub->id,
    ]);
    $payment = Payment::create([
        'payment_invoice_id' => $inv->id,
        'payment_name' => 'Test Payer',
        'payment_email' => 'payer@test.com',
        'payment_phone' => '08123456789',
        'payment_amount' => 1000000,
        'payment_status' => 'pending',
        'payment_timestamp' => now(),
    ]);

    // GET request to /delete must fail with 405 Method Not Allowed
    $res = $this->actingAs($superAdmin)->get(route('back.finance.verification.delete', $payment->id));
    $res->assertStatus(405);
    expect(Payment::find($payment->id))->not->toBeNull();
});

it('forbids non-super-admin from deleting payment via HTTP DELETE', function () {
    $keuangan = User::factory()->create();
    $keuangan->assignRole('keuangan');

    $journal = createSecJournal('journal-pay-del-2');
    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Issue Pay Del 2',
    ]);
    $sub = createSecSubmission($issue->id, 'SUB-PAY-DEL-2', 'Payment Deletion Article 2');
    $inv = PaymentInvoice::create([
        'invoice_number' => '8889',
        'payment_percent' => 100,
        'payment_amount' => 1000000,
        'payment_due_date' => now()->addDays(3),
        'submission_id' => $sub->id,
    ]);
    $payment = Payment::create([
        'payment_invoice_id' => $inv->id,
        'payment_name' => 'Test Payer 2',
        'payment_email' => 'payer2@test.com',
        'payment_phone' => '08123456789',
        'payment_amount' => 1000000,
        'payment_status' => 'pending',
        'payment_timestamp' => now(),
    ]);

    // Finance user cannot delete payment
    $res = $this->actingAs($keuangan)->delete(route('back.finance.verification.delete', $payment->id));
    $res->assertStatus(403);
    expect(Payment::find($payment->id))->not->toBeNull();
});

it('allows super-admin to delete payment via HTTP DELETE', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');

    $journal = createSecJournal('journal-pay-del-3');
    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Issue Pay Del 3',
    ]);
    $sub = createSecSubmission($issue->id, 'SUB-PAY-DEL-3', 'Payment Deletion Article 3');
    $inv = PaymentInvoice::create([
        'invoice_number' => '8890',
        'payment_percent' => 100,
        'payment_amount' => 1000000,
        'payment_due_date' => now()->addDays(3),
        'submission_id' => $sub->id,
    ]);
    $payment = Payment::create([
        'payment_invoice_id' => $inv->id,
        'payment_name' => 'Test Payer 3',
        'payment_email' => 'payer3@test.com',
        'payment_phone' => '08123456789',
        'payment_amount' => 1000000,
        'payment_status' => 'pending',
        'payment_timestamp' => now(),
    ]);

    // Super-admin can delete payment
    $res = $this->actingAs($superAdmin)->delete(route('back.finance.verification.delete', $payment->id));
    $res->assertStatus(302);
    expect(Payment::find($payment->id))->toBeNull();
});

/*
|--------------------------------------------------------------------------
| Post Menu Access Control: Restricted to Super-Admin and Humas
|--------------------------------------------------------------------------
*/

it('forbids unauthorized roles from accessing Post menu routes', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    // Pengumuman
    $this->actingAs($editor)->get(route('back.announcement.index'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.announcement.create'))->assertStatus(403);

    // Event
    $this->actingAs($editor)->get(route('back.event.index'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.event.create'))->assertStatus(403);

    // Berita (News)
    $this->actingAs($editor)->get(route('back.news.index'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.news.create'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.news.category'))->assertStatus(403);
    $this->actingAs($editor)->get(route('back.news.comment'))->assertStatus(403);

    // Kata Sambutan (Welcome Speech / Tentang Kami)
    $this->actingAs($editor)->get(route('back.welcomeSpeech.index'))->assertStatus(403);

    // Menu Profil
    $this->actingAs($editor)->get(route('back.menu.profil.index'))->assertStatus(403);

    // Dashboard Berita
    $this->actingAs($editor)->get(route('back.dashboard.news'))->assertStatus(403);
});

it('allows humas and super-admin to access Post menu routes', function () {
    $humas = User::factory()->create();
    $humas->assignRole('humas');

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');

    foreach ([$humas, $superAdmin] as $user) {
        $this->actingAs($user)->get(route('back.announcement.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('back.event.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('back.news.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('back.news.category'))->assertStatus(200);
        $this->actingAs($user)->get(route('back.news.comment'))->assertStatus(200);
        $this->actingAs($user)->get(route('back.welcomeSpeech.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('back.menu.profil.index'))->assertStatus(200);
        $this->actingAs($user)->get(route('back.dashboard.news'))->assertStatus(200);
    }
});
