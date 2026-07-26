<?php

namespace Database\Seeders;

use App\Domain\Inbox\Models\InboxItem;
use App\Domain\Shared\Models\User;
use Illuminate\Database\Seeder;

class InboxItemSeeder extends Seeder
{
    /**
     * Seed sample InboxItems for development.
     *
     * Creates a mix of unprocessed and processed items for the first user
     * so the Inbox UI can be exercised immediately after seeding.
     * Skip silently when no users exist.
     */
    public function run(): void
    {
        $user = User::first();

        if (! $user) {
            $this->command->warn('No users found. Skipping InboxItemSeeder.');

            return;
        }

        // Unprocessed items — appear in the Inbox Triage list
        $unprocessed = [
            'Buat video tentang cara menggunakan Obsidian untuk produktivitas harian',
            'Cek dokumentasi Laravel 12 tentang fitur Typed Properties baru',
            'Kirim email ke klien A soal revisi desain logo',
            'Belajar dasar-dasar animasi CSS: keyframes dan transitions',
            'Riset alat manajemen keuangan pribadi yang cocok untuk freelancer',
            'Rekap catatan meeting minggu lalu sebelum lupa',
            'Beli charger laptop baru — yang lama mulai rusak',
        ];

        foreach ($unprocessed as $content) {
            InboxItem::factory()
                ->forUser($user)
                ->unprocessed()
                ->withContent($content)
                ->create();
        }

        // Processed items — tidak muncul di list tapi berguna untuk testing filter
        $processed = [
            'Daftar akun GitHub Student Pack',
            'Review PR dari kolega tentang refactor Auth module',
            'Update dependensi Composer ke versi terbaru',
        ];

        foreach ($processed as $content) {
            InboxItem::factory()
                ->forUser($user)
                ->processed()
                ->withContent($content)
                ->create();
        }

        $this->command->info(
            sprintf(
                'InboxItem seeder: %d unprocessed + %d processed untuk user: %s',
                count($unprocessed),
                count($processed),
                $user->email
            )
        );
    }
}
