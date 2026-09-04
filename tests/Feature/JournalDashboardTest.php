<?php

use App\Models\Issue;
use App\Models\Journal;
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
    Role::firstOrCreate(['name' => 'editor']);
    Role::firstOrCreate(['name' => 'keuangan']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

function createTestJournal(array $attributes = []): Journal
{
    return Journal::create(array_merge([
        'name' => 'Al-Hikmah Journal',
        'title' => 'Al-Hikmah: Jurnal Studi Keislaman',
        'context_id' => 101,
        'url' => 'https://journal.test/al-hikmah',
        'url_path' => 'al-hikmah',
        'author_fee' => 500000,
        'api_key' => 'secret_api_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
        'type' => 'journal',
    ], $attributes));
}

it('shows journal dashboard page for authenticated user with select2 and optgroups', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createTestJournal([
        'name' => 'Al-Hikmah Journal',
        'url_path' => 'al-hikmah',
        'type' => 'journal',
    ]);

    $proceeding = createTestJournal([
        'name' => 'Proceeding Seminar Nasional',
        'title' => 'Proceeding Semnas',
        'context_id' => 104,
        'url' => 'https://journal.test/semnas',
        'url_path' => 'semnas',
        'type' => 'proceeding',
    ]);

    $response = $this->actingAs($user)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.dashboard.journal'));

    $response->assertStatus(200);
    $response->assertSee('Dashboard Jurnal');
    $response->assertSee('data-control="select2"', false);
    $response->assertSee('<optgroup label="Jurnal / E-Journal">', false);
    $response->assertSee('<optgroup label="Proceeding">', false);
    $response->assertSee('Al-Hikmah Journal');
    $response->assertSee('Proceeding Seminar Nasional');
});

it('returns accurate statistics and chart data via api', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createTestJournal([
        'name' => 'Jurnal Teknologi',
        'title' => 'Jurnal Teknologi dan Terapan',
        'context_id' => 102,
        'url' => 'https://journal.test/jteknologi',
        'url_path' => 'jteknologi',
        'author_fee' => 500000,
    ]);

    $issue1 = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Vol 1 No 1',
        'author_fee' => 500000,
    ]);

    $issue2 = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '2',
        'year' => '2026',
        'title' => 'Vol 1 No 2',
        'author_fee' => 500000,
    ]);

    // Submission 1: Published & Lunas (100%)
    Submission::create([
        'issue_id' => $issue1->id,
        'submission_id' => 'SUB-001',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'paid',
    ]);

    // Submission 2: Belum Publish & Belum Bayar (0%)
    Submission::create([
        'issue_id' => $issue1->id,
        'submission_id' => 'SUB-002',
        'status' => '1',
        'status_label' => 'Queued',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'pending',
    ]);

    // Submission 3: Belum Publish & Free Charge
    Submission::create([
        'issue_id' => $issue1->id,
        'submission_id' => 'SUB-003',
        'status' => '1',
        'status_label' => 'In Review',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => true,
        'payment_status' => 'pending',
    ]);

    // Submission 4: Published & Belum Lunas (DP 60%)
    $sub4 = Submission::create([
        'issue_id' => $issue2->id,
        'submission_id' => 'SUB-004',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'pending',
    ]);

    PaymentInvoice::create([
        'submission_id' => $sub4->id,
        'invoice_number' => 'INV-004-60',
        'payment_percent' => 60,
        'payment_amount' => 300000,
        'is_paid' => true,
    ]);

    $response = $this->actingAs($user)
        ->withCookie('control_panel', 'journal')
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $journal->id]));

    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'journal' => [
                'id' => $journal->id,
                'name' => 'Jurnal Teknologi',
                'author_fee' => 500000,
            ],
            'summary' => [
                'total_submissions' => 4,
                'total_published' => 2,
                'total_unpublished' => 2,
                'lunas' => [
                    'count' => 1,
                    'amount' => 500000,
                ],
                'belum_lunas' => [
                    'count' => 1,
                    'paid_amount' => 300000,
                    'remaining_amount' => 200000,
                ],
                'belum_bayar' => [
                    'count' => 1,
                    'amount' => 500000,
                ],
                'free' => [
                    'count' => 1,
                ],
                'total_paid_received' => 800000,
                'total_outstanding' => 700000,
                'total_potential_revenue' => 1500000,
            ],
        ]);

    expect($response->json('issues_table'))->toHaveCount(2);
    expect($response->json('charts.issue_chart.categories'))->toHaveCount(2);
    expect($response->json('charts.payment_chart.series'))->toBe([1, 1, 1, 1]);
});

it('forbids unauthorized editor from accessing another journal statistics', function () {
    $editor = User::factory()->create();
    $editor->assignRole('editor');

    $permissionA = Permission::create(['name' => 'journal-a']);
    $permissionB = Permission::create(['name' => 'journal-b']);
    $editor->givePermissionTo($permissionA);

    $journalB = createTestJournal([
        'name' => 'Journal B',
        'title' => 'Journal B Title',
        'context_id' => 103,
        'url' => 'https://journal.test/b',
        'url_path' => 'journal-b',
    ]);

    $response = $this->actingAs($editor)
        ->withCookie('control_panel', 'journal')
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $journalB->id]));

    $response->assertStatus(403);
});

it('allows admin-ejournal to access only journal type and restricts other types', function () {
    Role::firstOrCreate(['name' => 'admin-ejournal']);
    $adminEjournal = User::factory()->create();
    $adminEjournal->assignRole('admin-ejournal');

    $journal = createTestJournal([
        'name' => 'E-Journal 1',
        'url_path' => 'ejournal-1',
        'context_id' => 201,
        'type' => 'journal',
    ]);

    $proceeding = createTestJournal([
        'name' => 'Proceeding 1',
        'url_path' => 'proceeding-1',
        'context_id' => 202,
        'type' => 'proceeding',
    ]);

    // Page view should show only journal type in dropdown
    $response = $this->actingAs($adminEjournal)
        ->get(route('back.dashboard.journal'));

    $response->assertStatus(200);
    $response->assertSee('E-Journal 1');
    $response->assertDontSee('Proceeding 1');

    // Stat for journal type should succeed
    $this->actingAs($adminEjournal)
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $journal->id]))
        ->assertStatus(200);

    // Stat for proceeding type should be forbidden
    $this->actingAs($adminEjournal)
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $proceeding->id]))
        ->assertStatus(403);
});

it('allows admin-proceeding to access only proceeding type and restricts other types', function () {
    Role::firstOrCreate(['name' => 'admin-proceeding']);
    $adminProceeding = User::factory()->create();
    $adminProceeding->assignRole('admin-proceeding');

    $journal = createTestJournal([
        'name' => 'E-Journal 2',
        'url_path' => 'ejournal-2',
        'context_id' => 203,
        'type' => 'journal',
    ]);

    $proceeding = createTestJournal([
        'name' => 'Proceeding 2',
        'url_path' => 'proceeding-2',
        'context_id' => 204,
        'type' => 'proceeding',
    ]);

    // Stat for proceeding type should succeed
    $this->actingAs($adminProceeding)
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $proceeding->id]))
        ->assertStatus(200);

    // Stat for journal type should be forbidden
    $this->actingAs($adminProceeding)
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $journal->id]))
        ->assertStatus(403);
});

it('allows admin-student-research-hub to access only student_research_hub type', function () {
    Role::firstOrCreate(['name' => 'admin-student-research-hub']);
    $adminSrh = User::factory()->create();
    $adminSrh->assignRole('admin-student-research-hub');

    $srh = createTestJournal([
        'name' => 'SRH 1',
        'url_path' => 'srh-1',
        'context_id' => 205,
        'type' => 'student_research_hub',
    ]);

    $journal = createTestJournal([
        'name' => 'E-Journal 3',
        'url_path' => 'ejournal-3',
        'context_id' => 206,
        'type' => 'journal',
    ]);

    // Stat for SRH type should succeed
    $this->actingAs($adminSrh)
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $srh->id]))
        ->assertStatus(200);

    // Stat for journal type should be forbidden
    $this->actingAs($adminSrh)
        ->getJson(route('back.dashboard.journal.stat', ['journal_id' => $journal->id]))
        ->assertStatus(403);
});

it('forbids users without allowed roles from accessing journal dashboard', function () {
    $keuangan = User::factory()->create();
    $keuangan->assignRole('keuangan');

    $this->actingAs($keuangan)
        ->get(route('back.dashboard.journal'))
        ->assertStatus(403);

    $this->actingAs($keuangan)
        ->getJson(route('back.dashboard.journal.stat'))
        ->assertStatus(403);
});

