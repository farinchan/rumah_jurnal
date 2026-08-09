<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs in user with remember me enabled', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => bcrypt('password123'),
    ]);

    $response = $this->post(route('login.post'), [
        'login' => 'user@example.com',
        'password' => 'password123',
        'remember' => '1',
    ]);

    $response->assertRedirect('/');
    $this->assertAuthenticatedAs($user);

    // Verify remember token is set on user
    expect($user->fresh()->remember_token)->not->toBeNull();
});
