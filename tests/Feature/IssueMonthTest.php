<?php

use App\Models\Issue;
use App\Models\Journal;
use App\Models\SettingWebsite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
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

function createIssueMonthTestEnvironment(string $path = 'jmonth'): array
{
    Permission::firstOrCreate(['name' => $path]);

    $user = User::factory()->create();
    $user->assignRole('super-admin');
    $user->givePermissionTo($path);

    $journal = Journal::create([
        'name' => 'Journal Month Test',
        'title' => 'Journal Month Test Title',
        'context_id' => 200,
        'url' => 'https://journal.test/' . $path,
        'url_path' => $path,
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'secret_key',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    return compact('user', 'journal');
}

it('stores an issue with a valid month (1-12) via issueStore', function () {
    $env = createIssueMonthTestEnvironment('jstore');

    $response = $this->actingAs($env['user'])
        ->post(route('back.journal.issue.store', $env['journal']->url_path), [
            'volume' => '10',
            'number' => '1',
            'year' => '2026',
            'month' => 6,
            'title' => 'Edisi Juni 2026',
            'description' => 'Deskripsi edisi',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $issue = Issue::where('title', 'Edisi Juni 2026')->first();
    expect($issue)->not->toBeNull();
    expect($issue->month)->toBe(6);
    expect($issue->month_name)->toBe('Juni');
    expect($issue->publication_month)->toBe(6);
});

it('stores an issue with null month when month is omitted or empty', function () {
    $env = createIssueMonthTestEnvironment('jstorenull');

    $response = $this->actingAs($env['user'])
        ->post(route('back.journal.issue.store', $env['journal']->url_path), [
            'volume' => '10',
            'number' => '2',
            'year' => '2026',
            'month' => '',
            'title' => 'Edisi Tanpa Bulan',
        ]);

    $response->assertSessionHasNoErrors();

    $issue = Issue::where('title', 'Edisi Tanpa Bulan')->first();
    expect($issue)->not->toBeNull();
    expect($issue->month)->toBeNull();
    expect($issue->month_name)->toBeNull();
});

it('updates an issue month via issueUpdate', function () {
    $env = createIssueMonthTestEnvironment('jupdate');

    $issue = $env['journal']->issues()->create([
        'volume' => '5',
        'number' => '1',
        'year' => '2026',
        'month' => 3,
        'title' => 'Edisi Awal',
    ]);

    expect($issue->month)->toBe(3);
    expect($issue->month_name)->toBe('Maret');

    $response = $this->actingAs($env['user'])
        ->put(route('back.journal.issue.update', [$env['journal']->url_path, $issue->id]), [
            'volume' => '5',
            'number' => '1',
            'year' => '2026',
            'month' => 11,
            'title' => 'Edisi Diperbarui November',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    $issue->refresh();
    expect($issue->month)->toBe(11);
    expect($issue->month_name)->toBe('November');
});

it('allows clearing month back to null via issueUpdate', function () {
    $env = createIssueMonthTestEnvironment('jclear');

    $issue = $env['journal']->issues()->create([
        'volume' => '5',
        'number' => '2',
        'year' => '2026',
        'month' => 8,
        'title' => 'Edisi Agustus',
    ]);

    $response = $this->actingAs($env['user'])
        ->put(route('back.journal.issue.update', [$env['journal']->url_path, $issue->id]), [
            'volume' => '5',
            'number' => '2',
            'year' => '2026',
            'month' => '',
            'title' => 'Edisi Agustus Cleared',
        ]);

    $response->assertSessionHasNoErrors();

    $issue->refresh();
    expect($issue->month)->toBeNull();
    expect($issue->month_name)->toBeNull();
});

it('rejects invalid month numbers outside 1-12', function () {
    $env = createIssueMonthTestEnvironment('jinvalid');

    // Month 13 should fail
    $response = $this->actingAs($env['user'])
        ->post(route('back.journal.issue.store', $env['journal']->url_path), [
            'volume' => '1',
            'number' => '1',
            'year' => '2026',
            'month' => 13,
            'title' => 'Edisi Invalid Month',
        ]);

    $response->assertSessionHasErrors(['month']);

    // Month 0 should fail
    $responseZero = $this->actingAs($env['user'])
        ->post(route('back.journal.issue.store', $env['journal']->url_path), [
            'volume' => '1',
            'number' => '1',
            'year' => '2026',
            'month' => 0,
            'title' => 'Edisi Invalid Month Zero',
        ]);

    $responseZero->assertSessionHasErrors(['month']);
});

it('renders month field in index modal and setting page with only Juni and Desember without numbers', function () {
    $env = createIssueMonthTestEnvironment('jviews');

    $issue = $env['journal']->issues()->create([
        'volume' => '2',
        'number' => '1',
        'year' => '2026',
        'month' => 6,
        'title' => 'Edisi Juni',
    ]);

    // Check index view
    $responseIndex = $this->actingAs($env['user'])
        ->get(route('back.journal.index', $env['journal']->url_path));

    $responseIndex->assertStatus(200);
    $responseIndex->assertSee('Bulan Terbit');
    $responseIndex->assertSee('name="month"', false);
    $responseIndex->assertSee('(Juni 2026): Edisi Juni');
    $responseIndex->assertSee('<option value="6"', false);
    $responseIndex->assertSee('<option value="12"', false);
    $responseIndex->assertDontSee('6 - Juni');
    $responseIndex->assertDontSee('12 - Desember');
    $responseIndex->assertDontSee('>Januari<', false);
    $responseIndex->assertDontSee('>Maret<', false);

    // Check detail setting view
    $responseSetting = $this->actingAs($env['user'])
        ->get(route('back.journal.setting.index', [$env['journal']->url_path, $issue->id]));

    $responseSetting->assertStatus(200);
    $responseSetting->assertSee('Bulan Terbit');
    $responseSetting->assertSee('name="month"', false);
    $responseSetting->assertSee('<option value="6"', false);
    $responseSetting->assertSee('<option value="12"', false);
    $responseSetting->assertDontSee('6 - Juni');
    $responseSetting->assertDontSee('12 - Desember');
    $responseSetting->assertDontSee('>Januari<', false);
    $responseSetting->assertDontSee('>Maret<', false);
});
