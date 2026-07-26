# LARAVEL_RULES.md

*(Konvensi spesifik framework — pelengkap ARCHITECTURE_RULES.md, ringkasan dari TDD)*

## Model

- `$fillable` eksplisit — **jangan** pakai `$guarded = []`.
- Method Model dibatasi hanya untuk logika milik entitas itu sendiri (accessor/mutator ringan). Logika lintas-entitas selalu di Action.
- Setiap kolom status di-cast ke backed Enum lewat `casts()`.

## Action

- Satu Action class, satu tanggung jawab, method publik `execute()` (atau `__invoke()`).
- Menerima input eksplisit (Model, DTO, atau parameter primitif) — **jangan** menerima `Request` langsung di dalam Action (agar bisa dipanggil dari Controller, Job, maupun Command dengan cara sama).
- Dispatch Event di akhir Action jika ada efek lintas modul (lihat Event & Listener Mapping TDD bagian 13).

## Form Request

- Satu Form Request per aksi (`StoreTaskRequest`, `UpdateTaskRequest` terpisah) — jangan gabung Create & Update dalam satu class jika aturan validasinya berbeda.
- Aturan validasi mengikuti persis FSD per fitur — FSD adalah satu-satunya sumber kebenaran aturan bisnis.

## Policy

- Satu Policy per entitas utama.
- Aturan dasar MVP: `view/update/delete → $entity->user_id === $user->id`, selain itu `false`.
- Dipanggil di titik masuk Controller/Livewire — **Action tidak melakukan pengecekan otorisasi ulang**.
- Didaftarkan di `AuthServiceProvider::$policies` — Laravel **tidak** melakukan auto-discovery Policy jika entri ini ada.

**Dua pola standar yang dipakai proyek ini:**

Pola A — entitas yang dimiliki user (Task, Project, Note, dst.) — gunakan ini untuk semua Policy modul:
```php
// app/Policies/TaskPolicy.php  ← salin pola ini untuk setiap entitas baru
namespace App\Policies;

use App\Domain\Shared\Models\User;
use App\Domain\Tasks\Models\Task;    // ganti dengan Model entitas terkait

class TaskPolicy
{
    public function view(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function update(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }

    public function delete(User $user, Task $task): bool
    {
        return $user->id === $task->user_id;
    }
}
```

Pola B — User-on-self (hanya untuk `UserPolicy`):
```php
// $target adalah User lain — user hanya boleh mengelola dirinya sendiri
public function view(User $user, User $target): bool
{
    return $user->id === $target->id;
}
```

> **Aturan:** selalu tambahkan entri baru ke `AuthServiceProvider::$policies` saat membuat Policy baru.
> **Jangan** pakai `$this->authorize()` di dalam Action — otorisasi hanya di lapisan Controller/Livewire.

## Notification & Queue

- Notification dengan channel `database` untuk in-app (tersimpan otomatis sebagai riwayat, tampil di Dashboard).
- Job dipisah pola **scanner** (dijadwalkan, memindai kondisi) dan **sender** (dipicu scanner, satu per entitas) — kegagalan satu sender tidak menggagalkan batch.

## Testing

- **PestPHP**, bukan PHPUnit murni.
- Unit test untuk Action berisiko tinggi (kalkulasi/transisi status) — prioritas tertinggi.
- Feature test untuk alur end-to-end per Use Case FSD.
- Factory dengan state method eksplisit per status (`Task::factory()->done()`), relasi opsional dibuat eksplisit lewat method (bukan otomatis).

## Coding Convention

- **PSR-12**, ditegakkan lewat **Laravel Pint** sebelum setiap commit.
- Tidak ada method Action >30–40 baris tanpa alasan kuat yang dicatat sebagai komentar.

## Checklist Sebelum Pull Request/Merge

- [ ] `php artisan pint` dijalankan, tidak ada perubahan style tersisa.
- [ ] Tidak ada `$guarded = []` di Model manapun.
- [ ] Semua Action baru memiliki unit test.
- [ ] Semua Form Request baru sesuai aturan validasi FSD terkait.
