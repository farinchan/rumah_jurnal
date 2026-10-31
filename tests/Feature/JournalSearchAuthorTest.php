<?php

use App\Models\Issue;
use App\Models\Journal;
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
        'about' => 'Pusat Publikasi',
    ]);
});

function createSearchAuthorTestEnvironment(): array
{
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Sains dan Teknologi',
        'title' => 'Jurnal Sains dan Teknologi Title',
        'context_id' => 1234,
        'url' => 'https://journal.test/jst',
        'url_path' => 'jst',
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'secret_api_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    // Issue 1
    $issue1 = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '1',
        'year' => '2025',
        'title' => 'Edisi Pertama 2025',
        'author_fee' => 500000,
    ]);

    // Issue 2
    $issue2 = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '1',
        'number' => '2',
        'year' => '2025',
        'title' => 'Edisi Kedua 2025',
        'author_fee' => 500000,
    ]);

    // Submission in Issue 1
    $submission1 = Submission::create([
        'issue_id' => $issue1->id,
        'submission_id' => 'SUB-101',
        'fullTitle' => ['en' => 'Implementasi Machine Learning pada Sistem Cerdas'],
        'authorsString' => 'Dr. Muhammad Fajri, M.Kom.',
        'author_nik' => '1234567890123456',
        'author_bank_name' => 'Bank Mandiri',
        'author_bank_account' => '111222333444',
        'status' => '3',
        'status_label' => 'Published',
        'payment_status' => 'paid',
        'lastModified' => now()->toDateTimeString(),
    ]);

    // Submission in Issue 2
    $submission2 = Submission::create([
        'issue_id' => $issue2->id,
        'submission_id' => 'SUB-202',
        'fullTitle' => ['en' => 'Analisis Kriptografi Modern pada Jaringan Nirkabel'],
        'authorsString' => 'Prof. Dr. Siti Rahmah, M.Eng.',
        'author_nik' => '9876543210987654',
        'author_bank_name' => 'BSI',
        'author_bank_account' => '999888777666',
        'status' => '1',
        'status_label' => 'Queued',
        'payment_status' => 'pending',
        'lastModified' => now()->toDateTimeString(),
    ]);

    return compact('user', 'journal', 'issue1', 'issue2', 'submission1', 'submission2');
}

it('renders cari penulis button and search modal on journal index page', function () {
    $env = createSearchAuthorTestEnvironment();

    $response = $this->actingAs($env['user'])
        ->get(route('back.journal.index', $env['journal']->url_path));

    $response->assertStatus(200);
    $response->assertSee('Cari Penulis');
    $response->assertSee('id="btn_search_author"', false);
    $response->assertSee('id="modal_search_author"', false);
    $response->assertSee('Cari Penulis & Submission', false);
    $response->assertSee('id="input_search_author_keyword"', false);
    $response->assertSee('id="select_search_by"', false);
    $response->assertSee('id="select_search_issue"', false);
    $response->assertSee('id="btn_reset_search_author"', false);
});

it('searches across any issue in the journal by submission_id', function () {
    $env = createSearchAuthorTestEnvironment();

    $response = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'q' => 'SUB-202',
            'search_by' => 'submission_id',
        ]));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'count' => 1,
    ]);

    $data = $response->json('data');
    expect($data[0]['submission_id'])->toBe('SUB-202');
    expect($data[0]['authors_string'])->toContain('Siti Rahmah');
    expect($data[0]['issue_title'])->toContain('Edisi Kedua 2025');
});

it('searches across any issue in the journal by author name', function () {
    $env = createSearchAuthorTestEnvironment();

    $response = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'q' => 'Fajri',
            'search_by' => 'author',
        ]));

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
        'count' => 1,
    ]);

    $data = $response->json('data');
    expect($data[0]['submission_id'])->toBe('SUB-101');
    expect($data[0]['authors_string'])->toContain('Muhammad Fajri');
    expect($data[0]['issue_title'])->toContain('Edisi Pertama 2025');
});

it('searches author strictly by authorsString and not by other fields', function () {
    $env = createSearchAuthorTestEnvironment();

    // Search by authorsString matches
    $response = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'q' => 'Siti Rahmah',
            'search_by' => 'author',
        ]));

    $response->assertStatus(200);
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['submission_id'])->toBe('SUB-202');

    // Search by author NIK does NOT match because author search only uses authorsString
    $nikResponse = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'q' => '9876543210987654',
            'search_by' => 'author',
        ]));

    $nikResponse->assertStatus(200);
    $nikData = $nikResponse->json('data');
    expect($nikData)->toHaveCount(0);
});

it('filters search results by specific issue when issue_id is supplied', function () {
    $env = createSearchAuthorTestEnvironment();

    // Query for all without keyword in issue 2
    $response = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'issue_id' => $env['issue2']->id,
        ]));

    $response->assertStatus(200);
    $data = $response->json('data');
    expect($data)->toHaveCount(1);
    expect($data[0]['submission_id'])->toBe('SUB-202');
});

it('forbids unauthenticated or unauthorized users from searching authors', function () {
    $env = createSearchAuthorTestEnvironment();

    // Unauthenticated
    $unauthResponse = $this->getJson(route('back.journal.search-author', $env['journal']->url_path));
    $unauthResponse->assertStatus(401);

    // User without permission
    $otherUser = User::factory()->create();
    $forbiddenResponse = $this->actingAs($otherUser)
        ->getJson(route('back.journal.search-author', $env['journal']->url_path));
    $forbiddenResponse->assertStatus(403);
});

it('searches with individual filter inputs like in detail-article', function () {
    $env = createSearchAuthorTestEnvironment();

    // Search by filter_submission_id
    $resSub = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'filter_submission_id' => 'SUB-101',
        ]));
    $resSub->assertStatus(200);
    expect($resSub->json('data'))->toHaveCount(1);
    expect($resSub->json('data.0.submission_id'))->toBe('SUB-101');

    // Search by filter_author
    $resAuth = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'filter_author' => 'Siti Rahmah',
        ]));
    $resAuth->assertStatus(200);
    expect($resAuth->json('data'))->toHaveCount(1);
    expect($resAuth->json('data.0.submission_id'))->toBe('SUB-202');

    // Search by filter_title
    $resTitle = $this->actingAs($env['user'])
        ->getJson(route('back.journal.search-author', [
            'journal_path' => $env['journal']->url_path,
            'filter_title' => 'Machine Learning',
        ]));
    $resTitle->assertStatus(200);
    expect($resTitle->json('data'))->toHaveCount(1);
    expect($resTitle->json('data.0.submission_id'))->toBe('SUB-101');
});
