<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('registration page is available', function () {
    $response = $this->get('/register');

    $response->assertStatus(200)
        ->assertSee('Email address')
        ->assertSee('Confirm email');
});

test('user can register with matching email twice and be redirected to login', function () {
    $response = $this->post('/register', [
        'email' => 'newuser@example.com',
        'email_confirmation' => 'newuser@example.com',
    ]);

    $response->assertRedirect('/login');
    $this->assertDatabaseHas('users', [
        'email' => 'newuser@example.com',
    ]);
});

test('registration requires matching emails', function () {
    $response = $this->from('/register')->post('/register', [
        'email' => 'first@example.com',
        'email_confirmation' => 'second@example.com',
    ]);

    $response->assertRedirect('/register')
        ->assertSessionHasErrors('email');
});
