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
    Role::firstOrCreate(['name' => 'keuangan']);
    Role::firstOrCreate(['name' => 'editor']);
    Role::firstOrCreate(['name' => 'admin-ejournal']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

function createSyncTestJournal(string $path = 'j-sync'): Journal
{
    Permission::firstOrCreate(['name' => $path]);

    return Journal::create([
        'name' => 'Journal Sync Test',
        'title' => 'Journal Sync Test Title',
        'context_id' => 200,
        'url' => 'https://journal.test/' . $path,
        'url_path' => $path,
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);
}

function createSyncTestSubmission(array $attributes = []): Submission
{
    return Submission::create(array_merge([
        'submission_id' => 'SUB-' . Str::random(5),
        'title' => 'Test Article',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'pending',
    ], $attributes));
}

it('syncs 100% paid submissions from pending to paid', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createSyncTestJournal('j-sync-1');
    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Vol 1 No 1',
        'author_fee' => 500000,
    ]);

    // 1. Submission A: Invoice 100% is_paid = 1, payment_status = pending => Harus jadi PAID
    $subA = createSyncTestSubmission([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-A',
        'title' => 'Article A',
        'status' => '3',
        'payment_status' => 'pending',
        'free_charge' => 0,
    ]);
    PaymentInvoice::create([
        'submission_id' => $subA->id,
        'invoice_number' => '0001',
        'payment_percent' => 100,
        'payment_amount' => 500000,
        'is_paid' => 1,
    ]);

    // 2. Submission B: 60% + 40% is_paid = 1, payment_status = pending => Harus jadi PAID
    $subB = createSyncTestSubmission([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-B',
        'title' => 'Article B',
        'status' => '3',
        'payment_status' => 'pending',
        'free_charge' => 0,
    ]);
    PaymentInvoice::create([
        'submission_id' => $subB->id,
        'invoice_number' => '0002',
        'payment_percent' => 60,
        'payment_amount' => 300000,
        'is_paid' => 1,
    ]);
    PaymentInvoice::create([
        'submission_id' => $subB->id,
        'invoice_number' => '0003',
        'payment_percent' => 40,
        'payment_amount' => 200000,
        'is_paid' => 1,
    ]);

    // 3. Submission C: DP 60% only, payment_status = pending => Tetap PENDING (incomplete)
    $subC = createSyncTestSubmission([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-C',
        'title' => 'Article C',
        'status' => '3',
        'payment_status' => 'pending',
        'free_charge' => 0,
    ]);
    PaymentInvoice::create([
        'submission_id' => $subC->id,
        'invoice_number' => '0004',
        'payment_percent' => 60,
        'payment_amount' => 300000,
        'is_paid' => 1,
    ]);

    // 4. Submission D: Already paid => Tetap PAID
    $subD = createSyncTestSubmission([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-D',
        'title' => 'Article D',
        'status' => '3',
        'payment_status' => 'paid',
        'free_charge' => 0,
    ]);
    PaymentInvoice::create([
        'submission_id' => $subD->id,
        'invoice_number' => '0005',
        'payment_percent' => 100,
        'payment_amount' => 500000,
        'is_paid' => 1,
    ]);

    // 5. Submission E: Free charge => Tetap PENDING / Free charge
    $subE = createSyncTestSubmission([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-E',
        'title' => 'Article E',
        'status' => '3',
        'payment_status' => 'pending',
        'free_charge' => 1,
    ]);

    $response = $this->actingAs($user)->postJson(route('back.journal.sync-payments'), [
        'issue_id' => $issue->id,
    ]);

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'summary' => [
                'total_checked' => 5,
                'updated_count' => 2,
                'already_paid_count' => 1,
                'incomplete_count' => 1,
                'free_charge_count' => 1,
            ],
        ]);

    expect($subA->fresh()->payment_status)->toBe('paid');
    expect($subB->fresh()->payment_status)->toBe('paid');
    expect($subC->fresh()->payment_status)->toBe('pending');
    expect($subD->fresh()->payment_status)->toBe('paid');
    expect($subE->fresh()->payment_status)->toBe('pending');

    $updatedArticles = $response->json('updated_articles');
    expect($updatedArticles)->toHaveCount(2);
    $subIds = collect($updatedArticles)->pluck('submission_id')->all();
    expect($subIds)->toContain('SUB-A', 'SUB-B');
});

it('syncs only the targeted issue when issue_id is specified', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createSyncTestJournal('j-sync-2');
    $issue1 = Issue::create(['journal_id' => $journal->id, 'volume' => '1', 'number' => '1', 'year' => '2025', 'title' => 'Issue 1', 'author_fee' => 500000]);
    $issue2 = Issue::create(['journal_id' => $journal->id, 'volume' => '1', 'number' => '2', 'year' => '2025', 'title' => 'Issue 2', 'author_fee' => 500000]);

    $sub1 = createSyncTestSubmission(['issue_id' => $issue1->id, 'submission_id' => 'S1', 'payment_status' => 'pending', 'free_charge' => 0]);
    PaymentInvoice::create(['submission_id' => $sub1->id, 'invoice_number' => '0010', 'payment_percent' => 100, 'payment_amount' => 500000, 'is_paid' => 1]);

    $sub2 = createSyncTestSubmission(['issue_id' => $issue2->id, 'submission_id' => 'S2', 'payment_status' => 'pending', 'free_charge' => 0]);
    PaymentInvoice::create(['submission_id' => $sub2->id, 'invoice_number' => '0011', 'payment_percent' => 100, 'payment_amount' => 500000, 'is_paid' => 1]);

    // Sync only Issue 1
    $response = $this->actingAs($user)->postJson(route('back.journal.sync-payments'), [
        'issue_id' => $issue1->id,
    ]);

    $response->assertStatus(200);
    expect($response->json('summary.updated_count'))->toBe(1);
    expect($sub1->fresh()->payment_status)->toBe('paid');
    expect($sub2->fresh()->payment_status)->toBe('pending');
});

it('marks invoice is_paid as 1 when accepted payments exist and updates submission to paid', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createSyncTestJournal('j-sync-3');
    $issue = Issue::create(['journal_id' => $journal->id, 'volume' => '1', 'number' => '1', 'year' => '2025', 'title' => 'Issue 1', 'author_fee' => 500000]);

    $sub = createSyncTestSubmission(['issue_id' => $issue->id, 'submission_id' => 'S-PAY', 'payment_status' => 'pending', 'free_charge' => 0]);
    $invoice = PaymentInvoice::create(['submission_id' => $sub->id, 'invoice_number' => '0020', 'payment_percent' => 100, 'payment_amount' => 500000, 'is_paid' => 0]);

    Payment::create([
        'payment_invoice_id' => $invoice->id,
        'email' => 'payer@test.com',
        'name' => 'Payer Test',
        'payment_status' => 'accepted',
        'payment_amount' => 500000,
    ]);

    $response = $this->actingAs($user)->postJson(route('back.journal.sync-payments'), [
        'issue_id' => $issue->id,
    ]);

    $response->assertStatus(200);
    expect($response->json('summary.updated_count'))->toBe(1);
    expect($invoice->fresh()->is_paid)->toBe(1);
    expect($sub->fresh()->payment_status)->toBe('paid');
});

it('forbids unauthenticated and unauthorized users from syncing payments', function () {
    $responseGuest = $this->postJson(route('back.journal.sync-payments'));
    $responseGuest->assertStatus(401);

    $regularUser = User::factory()->create();
    // No role assigned
    $responseNoRole = $this->actingAs($regularUser)->postJson(route('back.journal.sync-payments'));
    $responseNoRole->assertStatus(403);
});

it('renders sinkron pembayaran button in detail-article and report views', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createSyncTestJournal('j-sync-view');
    $issue = Issue::create(['journal_id' => $journal->id, 'volume' => '1', 'number' => '1', 'year' => '2025', 'title' => 'Issue 1']);

    // Check detail-article page
    $resArticle = $this->actingAs($user)->get(route('back.journal.article.index', [$journal->url_path, $issue->id]));
    $resArticle->assertStatus(200);
    $resArticle->assertSee('Sinkron Pembayaran');
    $resArticle->assertSee('id="btn_sync_payments"', false);
    $resArticle->assertSee('modal_sync_payments');

    // Check report page
    $resReport = $this->actingAs($user)->get(route('back.finance.report.index'));
    $resReport->assertStatus(200);
    $resReport->assertSee('Sinkron Pembayaran');
    $resReport->assertSee('id="btn_sync_payments"', false);
    $resReport->assertSee('modal_sync_payments');
});
