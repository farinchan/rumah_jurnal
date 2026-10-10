<?php

use App\Models\Editor;
use App\Models\Issue;
use App\Models\Journal;
use App\Models\Reviewer;
use App\Models\SettingWebsite;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin']);
    Role::firstOrCreate(['name' => 'admin']);
    Role::firstOrCreate(['name' => 'editor']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

function createAuthorInfoTestContext(): array
{
    $user = User::factory()->create();
    $user->assignRole('super-admin');

    $journal = Journal::create([
        'name' => 'Jurnal Penulis Test',
        'title' => 'Jurnal Penulis Test Title',
        'context_id' => 888,
        'url' => 'https://journal.test/jpenulis',
        'url_path' => 'jpenulis',
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
        'title' => 'Edisi Uji Penulis',
        'author_fee' => 500000,
    ]);

    $submission = Submission::create([
        'issue_id' => $issue->id,
        'submission_id' => 'SUB-AUTHOR-01',
        'fullTitle' => ['en' => 'Judul Artikel Uji Form Penulis'],
        'authorsString' => 'Prof. Dr. Ahmad Dahlan',
        'status' => '3',
        'status_label' => 'Published',
        'lastModified' => now()->toDateTimeString(),
        'free_charge' => false,
        'payment_status' => 'pending',
        'author_nik' => '1234567890123456',
        'author_bank_name' => 'BSI',
        'author_bank_account' => '9876543210',
        'author_npwp' => '12.345.678.9-012.000',
        'author_golongan' => 'IV/a - Pembina',
    ]);

    return compact('user', 'journal', 'issue', 'submission');
}

it('renders the info tambahan penulis tab and form fields in modal view', function () {
    Http::fake([
        'https://journal.test/jpenulis/api/v1/submissions/SUB-AUTHOR-01/participants*' => Http::response([], 200),
    ]);

    $ctx = createAuthorInfoTestContext();

    $response = $this->actingAs($ctx['user'])
        ->get(route('back.journal.article.modal-view', [
            $ctx['journal']->url_path,
            $ctx['issue']->id,
            $ctx['submission']->id,
        ]));

    $response->assertStatus(200);
    $response->assertSee('Info Tambahan Penulis');
    $response->assertSee('kt_tab_pane_author_info_' . $ctx['submission']->id, false);
    $response->assertSee('name="author_nik"', false);
    $response->assertSee('name="author_bank_name"', false);
    $response->assertSee('name="author_bank_account"', false);
    $response->assertSee('name="author_npwp"', false);
    $response->assertSee('name="author_golongan"', false);
    $response->assertSee('1234567890123456');
    $response->assertSee('BSI');
    $response->assertSee('9876543210');
    $response->assertSee('12.345.678.9-012.000');
    $response->assertSee('IV/a - Pembina');
    $response->assertSee('Simpan Informasi Penulis');
});

it('updates author additional info when submitting author_info tab form', function () {
    $ctx = createAuthorInfoTestContext();

    $editor = Editor::create([
        'name' => 'Editor Satu',
        'issue_id' => $ctx['issue']->id,
    ]);
    $reviewer = Reviewer::create([
        'name' => 'Reviewer Satu',
        'issue_id' => $ctx['issue']->id,
    ]);

    $ctx['submission']->editors()->attach($editor->id);
    $ctx['submission']->reviewers()->attach($reviewer->id);

    $response = $this->actingAs($ctx['user'])
        ->put(route('back.journal.article.update', [
            $ctx['journal']->url_path,
            $ctx['issue']->id,
            $ctx['submission']->id,
        ]), [
            'form_type' => 'author_info',
            'author_nik' => '9998887776665554',
            'author_bank_name' => 'Bank Mandiri',
            'author_bank_account' => '111222333444',
            'author_npwp' => '99.888.777.6-555.000',
            'author_golongan' => 'IV/b - Pembina Tingkat I',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $ctx['submission']->refresh();
    expect($ctx['submission']->author_nik)->toBe('9998887776665554');
    expect($ctx['submission']->author_bank_name)->toBe('Bank Mandiri');
    expect($ctx['submission']->author_bank_account)->toBe('111222333444');
    expect($ctx['submission']->author_npwp)->toBe('99.888.777.6-555.000');
    expect($ctx['submission']->author_golongan)->toBe('IV/b - Pembina Tingkat I');

    // Verifies that editors and reviewers were preserved and not deleted
    expect($ctx['submission']->editors()->count())->toBe(1);
    expect($ctx['submission']->reviewers()->count())->toBe(1);
});

it('preserves author info when submitting general info from tab 1', function () {
    $ctx = createAuthorInfoTestContext();

    $editor = Editor::create([
        'name' => 'Editor Dua',
        'issue_id' => $ctx['issue']->id,
    ]);

    $response = $this->actingAs($ctx['user'])
        ->put(route('back.journal.article.update', [
            $ctx['journal']->url_path,
            $ctx['issue']->id,
            $ctx['submission']->id,
        ]), [
            'form_type' => 'general_info',
            'editor' => [$editor->id],
            'free_charge' => '1',
        ]);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    $ctx['submission']->refresh();
    expect($ctx['submission']->free_charge)->toBe(1);
    expect($ctx['submission']->editors()->count())->toBe(1);

    // Existing author info remains intact!
    expect($ctx['submission']->author_nik)->toBe('1234567890123456');
    expect($ctx['submission']->author_bank_name)->toBe('BSI');
    expect($ctx['submission']->author_bank_account)->toBe('9876543210');
    expect($ctx['submission']->author_npwp)->toBe('12.345.678.9-012.000');
    expect($ctx['submission']->author_golongan)->toBe('IV/a - Pembina');
});
