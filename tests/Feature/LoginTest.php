<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('login page is available', function () {
    $response = $this->get('/login');

    $response->assertStatus(200)
        ->assertSee('Email address');
});

test('user can login by email address only', function () {
    $user = User::factory()->create([
        'email' => 'jane@example.com',
        'password' => bcrypt('secret123'),
    ]);

    $response = $this->post('/login', [
        'email' => 'jane@example.com',
    ]);

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticatedAs($user);
});
