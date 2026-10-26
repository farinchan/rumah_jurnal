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
        'name' => 'Rumah Jurnal UIN Bukittinggi',
        'about' => 'Pusat Publikasi Ilmiah',
    ]);
});

function createLoaTestContext(): array
{
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal LoA Test',
        'title' => 'Jurnal Ilmiah LoA Test',
        'context_id' => 777,
        'url' => 'https://journal.test/jloa',
        'url_path' => 'jloa',
        'type' => 'journal',
        'author_fee' => 0,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
        'editor_chief_name' => 'Prof. Dr. Redaktur Utama, M.Ag.',
        'editor_chief_signature' => 'signatures/non_existent_file.png', // deliberately non-existent to test safe fallback
    ]);

    $issue = Issue::create([
        'journal_id' => $journal->id,
        'volume' => '10',
        'number' => '2',
        'year' => '2026',
        'title' => 'Edisi Khusus LoA',
        'author_fee' => 0,
    ]);

    $submission = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => '1683',
        'fullTitle' => ['en' => 'Analisis Validasi QR Code Dokumen Akademik'],
        'authors' => [
            [
                'id' => '3378',
                'name' => 'Dr. Fajri Rinaldi, M.Kom.',
                'affiliation' => 'Universitas Islam Negeri',
                'email' => 'fajri@example.com',
            ],
            [
                'id' => '3379',
                'name' => 'Ahmad Dahlan, M.Pd.',
                'affiliation' => 'Institut Agama Islam',
                'email' => 'ahmad@example.com',
            ],
        ],
        'authorsString' => 'Dr. Fajri Rinaldi, M.Kom., Ahmad Dahlan, M.Pd.',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'number' => '0001',
    ]);

    return compact('user', 'journal', 'issue', 'submission');
}

it('shows the search form on direct visit to loa validation page', function () {
    $response = $this->get(route('loa.validate'));

    $response->assertStatus(200);
    $response->assertSee('Verifikasi Keaslian LoA');
    $response->assertSee('Submission ID / Nomor Artikel:');
    $response->assertDontSee('Letter of Acceptance Tidak Valid');
});

it('validates loa successfully when searched by database primary key', function () {
    $ctx = createLoaTestContext();

    $response = $this->get(route('loa.validate', ['submission_id' => $ctx['submission']->id]));

    $response->assertStatus(200);
    $response->assertSee('Letter of Acceptance Terverifikasi');
    $response->assertSee('Analisis Validasi QR Code Dokumen Akademik');
    $response->assertSee('#1683');
    $response->assertSee('Dr. Fajri Rinaldi, M.Kom.');
    $response->assertSee('Ahmad Dahlan, M.Pd.');
});

it('validates loa successfully when searched by ojs submission_id', function () {
    $ctx = createLoaTestContext();

    $response = $this->get(route('loa.validate', ['submission_id' => '1683']));

    $response->assertStatus(200);
    $response->assertSee('Letter of Acceptance Terverifikasi');
    $response->assertSee('Analisis Validasi QR Code Dokumen Akademik');
    $response->assertSee('Dr. Fajri Rinaldi, M.Kom.');
});

it('validates specific author when author_id matches', function () {
    $ctx = createLoaTestContext();

    $response = $this->get(route('loa.validate', [
        'submission_id' => '1683',
        'author_id' => '3378',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Letter of Acceptance Terverifikasi');
    $response->assertSee('Dr. Fajri Rinaldi, M.Kom.');
    $response->assertSee('Afiliasi: Universitas Islam Negeri');
});

it('rejects validation when author_id does not belong to submission', function () {
    $ctx = createLoaTestContext();

    $response = $this->get(route('loa.validate', [
        'submission_id' => '1683',
        'author_id' => '999999',
    ]));

    $response->assertStatus(200);
    $response->assertSee('Letter of Acceptance Tidak Valid');
    $response->assertSee('Coba Verifikasi Ulang');
});

it('shows invalid card with retry search box when submission is not found', function () {
    $response = $this->get(route('loa.validate', ['submission_id' => '999999']));

    $response->assertStatus(200);
    $response->assertSee('Letter of Acceptance Tidak Valid');
    $response->assertSee('999999');
    $response->assertSee('Coba Verifikasi Ulang');
});

it('generates loa pdf with local offline qr code and handles missing signature file gracefully', function () {
    $ctx = createLoaTestContext();

    $response = $this->actingAs($ctx['user'])
        ->get(route('back.journal.loa.generate', $ctx['submission']->id));

    $response->assertStatus(200);
    $response->assertDownload('LoA-1683.pdf');
});
