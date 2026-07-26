# ARCHITECTURE_RULES.md

*(Ringkasan operasional dari TDD bagian 1–13 — untuk detail lengkap & alasan, baca TDD asli di `docs/context/reference/03-tdd.md`)*

## Struktur Wajib

```
app/
├── Domain/{Modul}/Models, Actions, Enums, Events, ValueObjects
├── Http/Controllers, Livewire/{Modul}, Requests, Middleware, Resources
├── Policies/
└── Providers/
```

## Aturan Tegas

1. **Modular monolith** — setiap modul FSD adalah domain folder di `app/Domain/`, bukan package Composer terpisah.
2. **Tanpa Repository Pattern** — Eloquent Model dipakai langsung di Action class. Jangan buat class `XRepository` untuk operasi CRUD biasa.
3. **Action Pattern** — satu class = satu operasi bisnis, satu method publik `execute()`/`__invoke()`. Jangan buat `TaskService` besar dengan banyak method tak berkaitan.
4. **DTO selektif** — hanya untuk Action yang menerima kombinasi data dari banyak sumber. Jangan pakai DTO untuk operasi CRUD sederhana satu-Model.
5. **Value Object selektif** — hanya untuk konsep yang punya perilaku/aturan sendiri (mis. `StreakCount`). Jangan bungkus setiap primitif dalam Value Object.
6. **Enum (backed enum PHP)** — wajib untuk setiap kolom status bertingkat. Jangan pakai string bebas/konstanta untuk status.
7. **Event/Listener untuk efek lintas modul** — modul tidak boleh memanggil Action modul lain secara langsung; gunakan Event yang di-dispatch dan Listener yang bereaksi. Lihat Event & Listener Mapping di TDD bagian 13.
8. **Namespace mengikuti nama domain**, bukan nama teknis generik (`App\Domain\Tasks\Actions\CompleteTaskAction`, bukan `App\Services\TaskService`).

## Modul yang Tidak Punya Entitas Database Sendiri

- **Focus Mode** — murni lapisan UI/state di atas `Task`. Jangan buat migration/Model untuk ini.
- **Dashboard** — read-only aggregation layer di atas Task/Habit/Reminder. Jangan buat tabel "dashboard_items" atau sejenisnya.
- **Search** — read-only consumer lintas modul. Jangan buat index/tabel search terpisah pada MVP (full-text search PostgreSQL adalah Future Enhancement, bukan MVP).

## Cache & Queue

- Redis untuk cache **dan** queue (satu komponen infrastruktur, dua kegunaan).
- Cache Dashboard: cache-aside + **invalidasi berbasis Event** (bukan TTL murni) — lihat TDD bagian 16.
- Seluruh scheduled job (reminder, agregasi Review) berjalan lewat Laravel Scheduler → Queue, pola scanner+sender terpisah (TDD bagian 15).

## Checklist Kepatuhan Arsitektur (jalankan sebelum merge modul baru)

- [ ] Tidak ada Repository class baru ditambahkan.
- [ ] Setiap status memakai backed Enum.
- [ ] Setiap efek lintas modul lewat Event/Listener, bukan pemanggilan langsung.
- [ ] Namespace class baru sesuai pola domain.
