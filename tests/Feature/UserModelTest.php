<?php

use App\Domain\Shared\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

uses(RefreshDatabase::class);

it('persists a user with an automatically generated ULID', function (): void {
    $user = User::factory()->create();

    expect($user->id)->toHaveLength(26);

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'email' => $user->email,
    ]);
});

it('hashes a password through the model cast', function (): void {
    $user = User::query()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'plain-password',
    ]);

    expect(Hash::check('plain-password', $user->password))->toBeTrue();
});
