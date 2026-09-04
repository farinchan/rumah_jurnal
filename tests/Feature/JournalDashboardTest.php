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

it('filters journal statistics by specific issue', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createTestJournal([
        'name' => 'Jurnal Filter Test',
        'url_path' => 'jfilter',
        'author_fee' => 400000,
    ]);

    $issueA = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '10',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Khusus A',
        'author_fee' => 500000,
    ]);

    $issueB = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '10',
        'number' => '2',
        'year' => '2025',
        'title' => 'Edisi Khusus B',
        'author_fee' => 600000,
    ]);

    Submission::create([
        'issue_id' => $issueA->id,
        'submission_id' => 'SUB-A1',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'paid',
    ]);

    Submission::create([
        'issue_id' => $issueB->id,
        'submission_id' => 'SUB-B1',
        'status' => '1',
        'status_label' => 'Queued',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'pending',
    ]);

    // Test page view sees the filter dropdown with Semua Issue option
    $pageResponse = $this->actingAs($user)
        ->get(route('back.dashboard.journal', ['journal_id' => $journal->id]));
    $pageResponse->assertStatus(200);
    $pageResponse->assertSee('id="issue_select"', false);
    $pageResponse->assertSee('<option value="all"', false);
    $pageResponse->assertSee('Semua Issue');
    $pageResponse->assertSee('Vol. 10 No. 1');
    $pageResponse->assertSee('Vol. 10 No. 2');

    // Test stat API filtered by issueA
    $apiResponse = $this->actingAs($user)
        ->getJson(route('back.dashboard.journal.stat', [
            'journal_id' => $journal->id,
            'issue_id' => $issueA->id,
        ]));

    $apiResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'journal' => [
                'id' => $journal->id,
                'author_fee' => 500000,
                'total_issues' => 2,
                'filtered_issues_count' => 1,
                'selected_issue' => [
                    'id' => $issueA->id,
                ],
            ],
            'summary' => [
                'is_issue_filtered' => true,
                'total_submissions' => 1,
                'total_published' => 1,
                'total_unpublished' => 0,
            ],
        ]);

    expect($apiResponse->json('issues_options'))->toHaveCount(2);
    expect($apiResponse->json('issues_table'))->toHaveCount(1);
    expect($apiResponse->json('issues_table.0.id'))->toBe($issueA->id);
});

it('provides modal triggers and serves submissions api for belum lunas and belum bayar', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = createTestJournal([
        'name' => 'Jurnal Modal Test',
        'url_path' => 'jmodal',
        'author_fee' => 1000000,
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Perdana Modal',
        'author_fee' => 1000000,
    ]);

    // 1. Submission Lunas
    $subLunas = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-LUNAS',
        'fullTitle' => ['en' => 'Artikel Lunas'],
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'paid',
    ]);
    \App\Models\PaymentInvoice::create([
        'submission_id' => $subLunas->id,
        'invoice_number' => 'INV-001',
        'payment_percent' => 100,
        'payment_amount' => 1000000,
        'is_paid' => true,
    ]);

    // 2. Submission Belum Lunas (DP 40%)
    $subBelumLunas = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-BELUM-LUNAS',
        'fullTitle' => ['en' => 'Artikel Belum Lunas'],
        'authorsString' => 'Budi Santoso',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'pending',
    ]);
    \App\Models\PaymentInvoice::create([
        'submission_id' => $subBelumLunas->id,
        'invoice_number' => 'INV-002-DP',
        'payment_percent' => 40,
        'payment_amount' => 400000,
        'is_paid' => true,
    ]);
    \App\Models\PaymentInvoice::create([
        'submission_id' => $subBelumLunas->id,
        'invoice_number' => 'INV-002-SISA',
        'payment_percent' => 60,
        'payment_amount' => 600000,
        'is_paid' => false,
    ]);

    // 3. Submission Belum Bayar (0%)
    $subBelumBayar = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-BELUM-BAYAR',
        'fullTitle' => ['en' => 'Artikel Belum Bayar'],
        'authorsString' => 'Dewi Lestari',
        'status' => '1',
        'status_label' => 'Queued',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'pending',
    ]);

    // 4. Submission Free Charge
    $subFree = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-FREE',
        'fullTitle' => ['en' => 'Artikel Gratis'],
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => true,
        'payment_status' => 'pending',
    ]);

    // Verify page view has modal and interactive cards
    $pageResponse = $this->actingAs($user)
        ->get(route('back.dashboard.journal', ['journal_id' => $journal->id]));
    $pageResponse->assertStatus(200);
    $pageResponse->assertSee('id="card_belum_lunas"', false);
    $pageResponse->assertSee('id="card_belum_bayar"', false);
    $pageResponse->assertSee('id="modal_submission_payment_detail"', false);
    $pageResponse->assertSee(route('back.dashboard.journal.submissions'), false);

    // Verify API for Belum Lunas
    $belumLunasResponse = $this->actingAs($user)
        ->getJson(route('back.dashboard.journal.submissions', [
            'journal_id' => $journal->id,
            'issue_id' => $issue->id,
            'type' => 'belum_lunas',
        ]));

    $belumLunasResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'meta' => [
                'type' => 'belum_lunas',
                'type_label' => 'Belum Lunas (DP/Cicil)',
                'total_count' => 1,
                'total_fee' => 1000000,
                'total_paid' => 400000,
                'total_remaining' => 600000,
            ],
        ]);

    $submissionsBelumLunas = $belumLunasResponse->json('submissions');
    expect($submissionsBelumLunas)->toHaveCount(1);
    expect($submissionsBelumLunas[0]['submission_id'])->toBe('SUB-BELUM-LUNAS');
    expect($submissionsBelumLunas[0]['title'])->toBe('Artikel Belum Lunas');
    expect($submissionsBelumLunas[0]['authors'])->toBe('Budi Santoso');
    expect($submissionsBelumLunas[0]['paid_amount'])->toBe(400000);
    expect($submissionsBelumLunas[0]['remaining_amount'])->toBe(600000);
    expect($submissionsBelumLunas[0]['invoices'])->toHaveCount(2);

    // Verify API for Belum Bayar
    $belumBayarResponse = $this->actingAs($user)
        ->getJson(route('back.dashboard.journal.submissions', [
            'journal_id' => $journal->id,
            'type' => 'belum_bayar',
        ]));

    $belumBayarResponse->assertStatus(200)
        ->assertJson([
            'success' => true,
            'meta' => [
                'type' => 'belum_bayar',
                'type_label' => 'Belum Bayar (0%)',
                'total_count' => 1,
                'total_fee' => 1000000,
                'total_paid' => 0,
                'total_remaining' => 1000000,
            ],
        ]);

    $submissionsBelumBayar = $belumBayarResponse->json('submissions');
    expect($submissionsBelumBayar)->toHaveCount(1);
    expect($submissionsBelumBayar[0]['submission_id'])->toBe('SUB-BELUM-BAYAR');
    expect($submissionsBelumBayar[0]['title'])->toBe('Artikel Belum Bayar');
    expect($submissionsBelumBayar[0]['authors'])->toBe('Dewi Lestari');
    expect($submissionsBelumBayar[0]['paid_amount'])->toBe(0);
    expect($submissionsBelumBayar[0]['remaining_amount'])->toBe(1000000);

    // Verify unauthorized user is forbidden
    $unauthorizedUser = User::factory()->create();
    $this->actingAs($unauthorizedUser)
        ->getJson(route('back.dashboard.journal.submissions', [
            'journal_id' => $journal->id,
            'type' => 'belum_lunas',
        ]))
        ->assertStatus(403);
});



