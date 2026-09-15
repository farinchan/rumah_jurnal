<?php

use App\Models\Editor;
use App\Models\Journal;
use App\Models\Issue;
use App\Models\Reviewer;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin']);
    Role::firstOrCreate(['name' => 'editor']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

it('renders article index page with datatable configuration and search filter', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Datatable Test',
        'title' => 'Jurnal Datatable Test Title',
        'context_id' => 999,
        'url' => 'https://journal.test/jdatatable',
        'url_path' => 'jdatatable',
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Uji Datatable',
        'author_fee' => 500000,
    ]);

    $submission = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-DT-01',
        'fullTitle' => ['en' => 'Judul Artikel Uji Datatable'],
        'authorsString' => 'Penulis Pertama',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'paid',
    ]);

    $response = $this->actingAs($user)
        ->get(route('back.journal.article.index', [$journal->url_path, $issue->id]));

    $response->assertStatus(200);
    $response->assertSee('id="table_articles"', false);
    $response->assertSee('id="filter_submission_id"', false);
    $response->assertSee('id="filter_title"', false);
    $response->assertSee('$(\'#table_articles\').DataTable', false);
    $response->assertSee('serverSide: true', false);
    $response->assertSee('id="modal_view_article"', false);
    $response->assertSee('id="modal_action_article"', false);
    $response->assertSee('id="modal_delete_article"', false);
});

it('serves server-side yajra datatable json for articles', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Datatable Test',
        'title' => 'Jurnal Datatable Test Title',
        'context_id' => 999,
        'url' => 'https://journal.test/jdatatable',
        'url_path' => 'jdatatable',
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Uji Datatable',
        'author_fee' => 500000,
    ]);

    $editor = Editor::create([
        'issue_id' => $issue->id,
        'name' => 'Dr. Editor Satu',
        'affiliation' => 'Universitas Indonesia',
    ]);

    $reviewer = Reviewer::create([
        'issue_id' => $issue->id,
        'name' => 'Prof. Reviewer Satu',
        'affiliation' => 'ITB Bandung',
    ]);

    $submission = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-DT-01',
        'fullTitle' => ['en' => 'Judul Artikel Uji Datatable'],
        'authorsString' => 'Penulis Pertama',
        'author_nik' => '1234567890123456',
        'author_bank_name' => 'BSI',
        'author_bank_account' => '9876543210',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'paid',
    ]);

    $submission->editors()->attach($editor->id);
    $submission->reviewers()->attach($reviewer->id);

    $response = $this->actingAs($user)
        ->getJson(route('back.journal.article.datatable', [$journal->url_path, $issue->id]));

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'draw',
        'recordsTotal',
        'recordsFiltered',
        'data' => [
            '*' => [
                'submission_id',
                'submission',
                'author_info',
                'editor',
                'reviewer',
                'status',
                'payment',
                'action',
            ]
        ]
    ]);

    $data = $response->json('data.0');
    expect($data['submission_id'])->toBe('SUB-DT-01');
    expect($data['submission'])->toContain('Judul Artikel Uji Datatable');
    expect($data['submission'])->toContain('Penulis Pertama');
    expect($data['author_info'])->toContain('1234567890123456');
    expect($data['author_info'])->toContain('BSI');
    expect($data['editor'])->toContain('Dr. Editor Satu');
    expect($data['reviewer'])->toContain('Prof. Reviewer Satu');
    expect($data['status'])->toContain('badge-light-success');
    expect($data['payment'])->toContain('paid');
    expect($data['action'])->toContain('btn-view-article');
    expect($data['action'])->toContain('btn-action-article');
    expect($data['action'])->toContain('btn-delete-article');
    expect($data['action'])->toContain('modal-view');
    expect($data['action'])->toContain('modal-action');
});

it('renders dynamic modal view content for an article', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Modal Test',
        'title' => 'Jurnal Modal Test Title',
        'context_id' => 995,
        'url' => 'https://journal.test/jmodal',
        'url_path' => 'jmodal',
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Modal Test',
        'author_fee' => 500000,
    ]);

    $submission = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-MODAL-01',
        'fullTitle' => ['en' => 'Judul Dynamic Modal View'],
        'authorsString' => 'Penulis Dynamic',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('back.journal.article.modal-view', [$journal->url_path, $issue->id, $submission->id]));

    $response->assertStatus(200);
    $response->assertSee('SUB-MODAL-01');
    $response->assertSee('Judul Dynamic Modal View');
    $response->assertSee('Informasi');
    $response->assertSee('History Pembayaran');
});

it('renders dynamic modal action content for an article', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Modal Action Test',
        'title' => 'Jurnal Modal Action Test Title',
        'context_id' => 994,
        'url' => 'https://journal.test/jmodalaction',
        'url_path' => 'jmodalaction',
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Modal Action Test',
        'author_fee' => 500000,
    ]);

    $submission = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-ACTION-01',
        'fullTitle' => ['en' => 'Judul Dynamic Modal Action'],
        'authorsString' => 'Penulis Dynamic Action',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('back.journal.article.modal-action', [$journal->url_path, $issue->id, $submission->id]));

    $response->assertStatus(200);
    $response->assertSee('SUB-ACTION-01');
    $response->assertSee('Invoice');
    $response->assertSee('Letter of Acceptence (LOA)');
});

it('filters datatable results by search keyword', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Filter Test',
        'title' => 'Jurnal Filter Test Title',
        'context_id' => 998,
        'url' => 'https://journal.test/jfilter',
        'url_path' => 'jfilter',
        'type' => 'journal',
        'author_fee' => 0,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Filter',
        'author_fee' => 0,
    ]);

    Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'ALPHA-101',
        'fullTitle' => ['en' => 'Machine Learning Deep Dive'],
        'authorsString' => 'Alice Bob',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'BETA-202',
        'fullTitle' => ['en' => 'Blockchain Security Analysis'],
        'authorsString' => 'Charlie Dave',
        'status' => '1',
        'status_label' => 'Queued',
        'lastModified' => now()->toDateTimeString(),
    ]);

    $response = $this->actingAs($user)
        ->getJson(route('back.journal.article.datatable', [
            'journal_path' => $journal->url_path,
            'issue_id' => $issue->id,
            'search' => ['value' => 'Machine Learning'],
        ]));

    $response->assertStatus(200);
    expect($response->json('recordsFiltered'))->toBe(1);
    expect($response->json('data.0.submission_id'))->toBe('ALPHA-101');
});

it('serves datatable json when requesting article index with ajax header', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Ajax Test',
        'title' => 'Jurnal Ajax Test Title',
        'context_id' => 997,
        'url' => 'https://journal.test/jajax',
        'url_path' => 'jajax',
        'type' => 'journal',
        'author_fee' => 0,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Ajax',
        'author_fee' => 0,
    ]);

    Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-AJAX-01',
        'fullTitle' => ['en' => 'Ajax Test Article'],
        'authorsString' => 'Ajax Author',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
    ]);

    $response = $this->actingAs($user)
        ->get(route('back.journal.article.index', [$journal->url_path, $issue->id]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

    $response->assertStatus(200);
    $response->assertJsonStructure(['data', 'recordsTotal', 'recordsFiltered']);
    expect($response->json('data.0.submission_id'))->toBe('SUB-AJAX-01');
});

it('filters specifically by submission ID and by title separately', function () {
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Sep Test',
        'title' => 'Jurnal Sep Test Title',
        'context_id' => 996,
        'url' => 'https://journal.test/jsep',
        'url_path' => 'jsep',
        'type' => 'journal',
        'author_fee' => 300000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Sep',
        'author_fee' => 300000,
    ]);

    Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => '1001',
        'fullTitle' => ['en' => 'Artificial Intelligence in Healthcare'],
        'authorsString' => 'Author One',
        'status' => '3',
        'status_label' => 'Published',
        'payment_status' => 'paid',
        'lastModified' => now()->toDateTimeString(),
    ]);

    Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => '1002',
        'fullTitle' => ['en' => 'Quantum Computing 1001 Protocols'],
        'authorsString' => 'Author Two',
        'status' => '1',
        'status_label' => 'Queued',
        'payment_status' => 'pending',
        'lastModified' => now()->toDateTimeString(),
    ]);

    // Test 1: Search specifically by ID '1001'
    // Note that submission 1002 has '1001' in the title, but search by ID should ONLY match submission 1001!
    $resId = $this->actingAs($user)
        ->getJson(route('back.journal.article.datatable', [
            'journal_path' => $journal->url_path,
            'issue_id' => $issue->id,
            'filter_submission_id' => '1001',
        ]));

    $resId->assertStatus(200);
    expect($resId->json('recordsFiltered'))->toBe(1);
    expect($resId->json('data.0.submission_id'))->toBe('1001');

    // Test 2: Search specifically by Title 'Healthcare'
    $resTitle = $this->actingAs($user)
        ->getJson(route('back.journal.article.datatable', [
            'journal_path' => $journal->url_path,
            'issue_id' => $issue->id,
            'filter_title' => 'Healthcare',
        ]));

    $resTitle->assertStatus(200);
    expect($resTitle->json('recordsFiltered'))->toBe(1);
    expect($resTitle->json('data.0.submission_id'))->toBe('1001');

    // Test 3: Filter by status '1' (Queued)
    $resStatus = $this->actingAs($user)
        ->getJson(route('back.journal.article.datatable', [
            'journal_path' => $journal->url_path,
            'issue_id' => $issue->id,
            'filter_status' => '1',
        ]));

    $resStatus->assertStatus(200);
    expect($resStatus->json('recordsFiltered'))->toBe(1);
    expect($resStatus->json('data.0.submission_id'))->toBe('1002');

    // Test 4: Filter by payment 'pending'
    $resPayment = $this->actingAs($user)
        ->getJson(route('back.journal.article.datatable', [
            'journal_path' => $journal->url_path,
            'issue_id' => $issue->id,
            'filter_payment' => 'pending',
        ]));

    $resPayment->assertStatus(200);
    expect($resPayment->json('recordsFiltered'))->toBe(1);
    expect($resPayment->json('data.0.submission_id'))->toBe('1002');
});

