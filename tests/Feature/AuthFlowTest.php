<?php

namespace Tests\Feature;

use App\Domain\Shared\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('can login with valid credentials and redirect to /today', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'plain-password',
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'plain-password',
    ]);

    $response->assertRedirect('/today');
});

it('rejects login with invalid password', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'plain-password',
    ]);

    $response = $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertInvalid();
});

it('rate limits login attempts to 5 per minute per email+IP', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'plain-password',
    ]);

    for ($i = 0; $i < 6; $i++) {
        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrong-password',
        ]);
    }

    $response->assertStatus(429);
});

it('can logout successfully', function () {
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => 'plain-password',
    ]);

    $this->post('/login', [
        'email' => 'test@example.com',
        'password' => 'plain-password',
    ]);

    $response = $this->post('/logout');
    $response->assertRedirect('/');
});

it('guest cannot access /today', function () {
    $response = $this->get('/today');
    $response->assertRedirect('/login');
});
