<?php

use App\Models\Journal;
use App\Models\Issue;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin']);

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
    $response->assertSee('data-kt-article-table-filter="search"', false);
    $response->assertSee('$(\'#table_articles\').DataTable', false);
    $response->assertSee('SUB-DT-01');
    $response->assertSee('Judul Artikel Uji Datatable');
});
