<?php

use App\Models\Journal;
use App\Models\SettingWebsite;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

beforeEach(function () {
    Role::firstOrCreate(['name' => 'super-admin']);
    Role::firstOrCreate(['name' => 'admin-ejournal']);
    Role::firstOrCreate(['name' => 'admin-proceeding']);
    Role::firstOrCreate(['name' => 'admin-student-research-hub']);
    Role::firstOrCreate(['name' => 'editor']);
    Role::firstOrCreate(['name' => 'editor-proceeding']);
    Role::firstOrCreate(['name' => 'editor-student-research-hub']);
    Role::firstOrCreate(['name' => 'keuangan']);
    Role::firstOrCreate(['name' => 'keuangan-proceeding']);
    Role::firstOrCreate(['name' => 'keuangan-student-research-hub']);
    Role::firstOrCreate(['name' => 'humas']);
    Role::firstOrCreate(['name' => 'p2b']);
    Role::firstOrCreate(['name' => 'spi']);

    SettingWebsite::create([
        'name' => 'Rumah Jurnal Test',
        'about' => 'Platform Rumah Jurnal',
    ]);
});

test('super admin can assign and remove p2b and spi roles on user management', function () {
    $superAdmin = User::factory()->create();
    $superAdmin->assignRole('super-admin');

    // Create user with p2b and spi
    $response = $this->actingAs($superAdmin)->post(route('back.master.user.store'), [
        'name' => 'User P2B SPI',
        'email' => 'p2b_spi@test.com',
        'password' => 'password123',
        'role_p2b' => 1,
        'role_spi' => 1,
    ]);

    $response->assertRedirect();
    $user = User::where('email', 'p2b_spi@test.com')->first();
    expect($user)->not->toBeNull();
    expect($user->hasRole('p2b'))->toBeTrue();
    expect($user->hasRole('spi'))->toBeTrue();

    // Edit user to remove p2b and spi
    $updateResponse = $this->actingAs($superAdmin)->put(route('back.master.user.update', $user->id), [
        'name' => 'User P2B SPI Updated',
        'email' => 'p2b_spi@test.com',
        // role_p2b and role_spi not checked
    ]);

    $updateResponse->assertRedirect();
    $user->refresh();
    expect($user->hasRole('p2b'))->toBeFalse();
    expect($user->hasRole('spi'))->toBeFalse();
});

test('p2b role can access cashflow menu and dashboard but is forbidden from verification, report, and journal dashboard', function () {
    $p2bUser = User::factory()->create();
    $p2bUser->assignRole('p2b');

    // Can access Cashflow Menu
    $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.cashflow.index'))
        ->assertStatus(200);

    $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->getJson(route('back.finance.cashflow.datatable'))
        ->assertStatus(200);

    // Can access Cashflow Dashboard
    $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.dashboard.cashflow'))
        ->assertStatus(200);

    $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->getJson(route('back.dashboard.cashflow.stat'))
        ->assertStatus(200);

    // Forbidden from Verification
    $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.verification.index'))
        ->assertStatus(403);

    // Forbidden from Report
    $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.report.index'))
        ->assertStatus(403);

    // Forbidden from Journal Dashboard
    $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.dashboard.journal'))
        ->assertStatus(403);
});

test('spi role can access verification menu and journal dashboard but is forbidden from cashflow and report', function () {
    $spiUser = User::factory()->create();
    $spiUser->assignRole('spi');

    Journal::create([
        'name' => 'Journal Audit',
        'title' => 'Journal Audit',
        'context_id' => 201,
        'url' => 'https://journal.test/audit',
        'url_path' => 'audit',
        'type' => 'journal',
        'author_fee' => 500000,
        'api_key' => 'key_audit',
        'ojs_version' => '3.3',
        'last_sync' => now(),
    ]);

    // Can access Verification Menu
    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.verification.index'))
        ->assertStatus(200);

    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->getJson(route('back.finance.verification.datatable'))
        ->assertStatus(200);

    // Can access Journal Dashboard
    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.dashboard.journal'))
        ->assertStatus(200);

    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->getJson(route('back.dashboard.journal.stat'))
        ->assertStatus(200);

    // Forbidden from updating Verification
    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->put(route('back.finance.verification.update', 1))
        ->assertStatus(403);

    // Forbidden from Cashflow Menu
    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.cashflow.index'))
        ->assertStatus(403);

    // Forbidden from Cashflow Dashboard
    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.dashboard.cashflow'))
        ->assertStatus(403);

    // Forbidden from Report
    $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.report.index'))
        ->assertStatus(403);
});

test('sidebar menu shows appropriate links for p2b and spi roles', function () {
    $p2bUser = User::factory()->create();
    $p2bUser->assignRole('p2b');

    $spiUser = User::factory()->create();
    $spiUser->assignRole('spi');

    // P2B sidebar
    $p2bResponse = $this->actingAs($p2bUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.dashboard.cashflow'));
    $p2bResponse->assertStatus(200);
    $p2bResponse->assertSee(route('back.dashboard.cashflow'));
    $p2bResponse->assertSee(route('back.finance.cashflow.index'));
    $p2bResponse->assertDontSee(route('back.finance.verification.index'));
    $p2bResponse->assertDontSee(route('back.dashboard.journal'));

    // SPI sidebar
    $spiResponse = $this->actingAs($spiUser)
        ->withCookie('control_panel', 'journal')
        ->get(route('back.finance.verification.index'));
    $spiResponse->assertStatus(200);
    $spiResponse->assertSee(route('back.dashboard.journal'));
    $spiResponse->assertSee(route('back.finance.verification.index'));
    $spiResponse->assertDontSee(route('back.finance.cashflow.index'));
    $spiResponse->assertDontSee(route('back.dashboard.cashflow'));
});
