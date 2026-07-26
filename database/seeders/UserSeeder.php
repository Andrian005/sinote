<?php

namespace Database\Seeders;

use App\Domain\Shared\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the users table with default development user.
     *
     * Sesuai Database Spec Bagian H: membuat satu akun user default
     * untuk kebutuhan development lokal (single-user context).
     */
    public function run(): void
    {
        // Skip jika user sudah ada (mencegah duplikat saat re-seed)
        if (User::where('email', 'dev@personalos.test')->exists()) {
            $this->command->info('User dev@personalos.test already exists. Skipping.');

            return;
        }

        User::create([
            'name' => 'Developer User',
            'email' => 'dev@personalos.test',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        $this->command->info('Default development user created:');
        $this->command->line('  Email: dev@personalos.test');
        $this->command->line('  Password: password');
    }
}
