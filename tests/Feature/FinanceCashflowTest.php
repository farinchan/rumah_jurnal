<?php

use App\Models\Finance;
use App\Models\FinanceYear;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\PaymentInvoice;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin']);
    Role::firstOrCreate(['name' => 'keuangan']);
    Role::firstOrCreate(['name' => 'editor']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

function createCashflowSubmission(Issue $issue, float $amount = 1000000): Payment
{
    $submission = Submission::create([
        'issue_id' => $issue->id,
        'title' => 'Sample Article ' . rand(100, 999),
        'status' => 'paid',
        'submission_id' => rand(1000, 9999),
        'lastModified' => now()->toDateTimeString(),
    ]);

    $invoice = PaymentInvoice::create([
        'submission_id' => $submission->id,
        'invoice_number' => 'INV-' . rand(1000, 9999),
        'payment_amount' => $amount,
        'status' => 'paid',
    ]);

    return Payment::create([
        'payment_invoice_id' => $invoice->id,
        'name' => 'Author Name',
        'payment_status' => 'accepted',
        'payment_timestamp' => now()->toDateTimeString(),
        'created_at' => now(),
    ]);
}

test('it calculates cashflow distributions correctly in menu keuangan', function () {
    $user = User::factory()->create();
    $user->assignRole('keuangan');

    $financeYear = FinanceYear::create([
        'name' => 'Buku Kas 2026',
        'start_date' => now()->startOfYear()->toDateString(),
        'end_date' => now()->endOfYear()->toDateString(),
        'distribution_percentage' => 80,
        'is_active' => 1,
        'type_control' => 'journal',
    ]);

    $journal = Journal::create([
        'name' => 'Journal of Science',
        'title' => 'Journal of Science',
        'context_id' => 101,
        'url' => 'https://journal.test/jos',
        'url_path' => 'jos',
        'type' => 'journal',
        'author_fee' => 1000000,
        'api_key' => 'secret_key_jos',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'title' => 'Vol 1 No 1',
        'issue_id' => 1,
        'volume' => '1',
        'number' => '1',
        'year' => 2026,
    ]);

    // Accepted payment income = 1,000,000
    createCashflowSubmission($issue, 1000000);

    // Manual finance income = 500,000
    Finance::create([
        'name' => 'Sponsorship',
        'type' => 'income',
        'amount' => 500000,
        'date' => now()->toDateString(),
        'type_control' => 'journal',
    ]);

    // Manual finance expense = 300,000
    Finance::create([
        'name' => 'Hosting Server',
        'type' => 'expense',
        'amount' => 300000,
        'date' => now()->toDateString(),
        'type_control' => 'journal',
    ]);

    // Total Gross Income = 1,000,000 + 500,000 = 1,500,000
    // Total Outcome = 300,000
    // Total Balance = 1,500,000 - 300,000 = 1,200,000
    // Bagi Hasil Rumah Jurnal 80% = 1,500,000 * 80% = 1,200,000
    // Bagi Hasil BLU 20% = 1,500,000 * 20% = 300,000
    // Saldo Bersih Rumah Jurnal = 1,200,000 - 300,000 = 900,000

    $response = $this->actingAs($user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.cashflow.index'));

    $response->assertStatus(200);
    $response->assertViewHas('total_income_now', 1500000.0);
    $response->assertViewHas('total_outcome_now', 300000.0);
    $response->assertViewHas('total_balance_now', 1200000.0);
    $response->assertViewHas('total_distribution', 1200000.0);
    $response->assertViewHas('total_distributtion_other', 300000.0);
    $response->assertViewHas('distribution_rumah_jurnal_balance', 900000.0);
    $response->assertSee('Saldo Bersih Rumah Jurnal');
    $response->assertSee('Bagi Hasil Rumah Jurnal');
    $response->assertSee('Bagi Hasil BLU UIN Bukittinggi');
});

test('it stores and updates finance year with custom distribution percentage and control panel', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    // Create Year
    $storeResponse = $this->actingAs($user)
        ->withCookie('control_panel', 'journal')
        ->post(route('back.finance.cashflow-year.store'), [
            'name' => 'Tahun Buku 2026 Periode 1',
            'start_date' => '2026-01-01',
            'distribution_percentage' => 75,
        ]);

    $storeResponse->assertRedirect();

    $year = FinanceYear::where('type_control', 'journal')->where('name', 'Tahun Buku 2026 Periode 1')->first();
    expect($year)->not->toBeNull();
    expect($year->distribution_percentage)->toBe(75);
    expect($year->type_control)->toBe('journal');
    expect($year->is_active)->toBe(1);

    // Update Year
    $updateResponse = $this->actingAs($user)
        ->withCookie('control_panel', 'journal')
        ->put(route('back.finance.cashflow-year.edit'), [
            'id' => $year->id,
            'name' => 'Tahun Buku 2026 Direvisi',
            'start_date' => '2026-01-01',
            'distribution_percentage' => 85,
        ]);

    $updateResponse->assertRedirect();
    $year->refresh();
    expect($year->name)->toBe('Tahun Buku 2026 Direvisi');
    expect($year->distribution_percentage)->toBe(85);
});

test('it preserves total income and expense summaries in cashflow datatable when filtered by type', function () {
    $user = User::factory()->create();
    $user->assignRole('keuangan');

    Finance::create([
        'name' => 'Income Transaction',
        'type' => 'income',
        'amount' => 500000,
        'date' => now()->toDateString(),
        'type_control' => 'journal',
    ]);

    Finance::create([
        'name' => 'Expense Transaction',
        'type' => 'expense',
        'amount' => 200000,
        'date' => now()->toDateString(),
        'type_control' => 'journal',
    ]);

    // Request with filter type=expense
    $response = $this->actingAs($user)
        ->withCookie('control_panel', 'journal')
        ->getJson(route('back.finance.cashflow.datatable', [
            'type' => 'expense',
            'date_start' => now()->subDays(5)->toDateString(),
            'date_end' => now()->addDays(5)->toDateString(),
        ]));

    $response->assertStatus(200);
    $data = $response->json();

    // Summary reflects entire period despite filtering rows by expense
    expect($data['total_income'])->toBe(500000);
    expect($data['total_expense'])->toBe(200000);
    expect($data['total_balance'])->toBe(300000);
});

test('it exports cashflow excel filtered by active control panel', function () {
    $user = User::factory()->create();
    $user->assignRole('keuangan');

    $response = $this->actingAs($user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.cashflow.export', [
            'type' => 'all',
            'date_start' => now()->subMonth()->toDateString(),
            'date_end' => now()->toDateString(),
        ]));

    $response->assertStatus(200);
    $response->assertHeader('content-disposition');
});
