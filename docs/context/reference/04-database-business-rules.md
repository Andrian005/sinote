# PERSONAL OS — DATABASE & BUSINESS RULES SPECIFICATION
### Acuan Migration & Model Laravel — Berdasarkan Blueprint v1.0, FSD & TDD

---

## Keputusan Strategi Kunci Sebelum Spesifikasi Per Tabel

**ULID/UUID Strategy (berlaku untuk seluruh tabel):** Setiap tabel menggunakan **ULID** sebagai primary key tunggal (bukan auto-increment integer + UUID terpisah). ULID dipilih dibanding UUID v4 murni karena ULID **urut secara leksikografis berdasarkan waktu pembuatan** — ini menjaga performa index B-tree pada PostgreSQL (mirip auto-increment) sekaligus tetap tidak menyingkap urutan/jumlah data ke pengguna eksternal, sesuai keputusan skalabilitas di Blueprint bagian 8. Kolom primary key bernama `id`, tipe `char(26)`.

**Soft Delete (berlaku untuk seluruh entitas utama):** Kolom `deleted_at` (nullable timestamp) ditambahkan ke seluruh tabel entitas utama (bukan tabel pivot/log), sesuai prinsip keandalan data Blueprint bagian 3.4 — "data pribadi tidak boleh hilang".

**Timestamp (berlaku umum):** Seluruh tabel entitas utama memiliki `created_at` dan `updated_at` standar Laravel (Eloquent `timestamps()`). Tabel log (`habit_logs`) hanya memiliki `created_at` karena bersifat append-only.

**user_id Strategy:** Setiap tabel entitas utama memiliki kolom `user_id` (ULID, FK ke `users.id`) — keputusan skalabilitas paling penting dari Blueprint bagian 8 & 22, memastikan transisi single-user → multi-user tidak memerlukan migrasi skema.

---

# BAGIAN A — SPESIFIKASI TABEL PER ENTITAS

## A.1 Tabel `users`

**Tujuan:** Menyimpan akun pengguna aplikasi (fondasi auth — Shared domain).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `name` | varchar(255) | tidak | — | |
| `email` | varchar(255) | tidak | — | Unique |
| `email_verified_at` | timestamp | ya | null | Disiapkan untuk multi-user (Blueprint 19) |
| `password` | varchar(255) | tidak | — | Hashed |
| `remember_token` | varchar(100) | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Primary Key:** `id`. **Unique Constraint:** `email`. **Index:** `email`. **Foreign Key:** tidak ada (tabel induk). **Soft Delete:** tidak diterapkan pada `users` (penghapusan akun adalah operasi berisiko tinggi yang ditangani khusus di luar soft-delete standar — dicatat sebagai Future Enhancement terpisah, bukan default framework).

---

## A.2 Tabel `inbox_items`

**Tujuan:** Menampung capture spontan sebelum ditriase (FSD Modul 1).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `content` | text | tidak | — | 1–5000 karakter (FSD 1.1) |
| `status` | varchar(20) enum | tidak | `'unprocessed'` | `unprocessed` \| `processed` \| `discarded` |
| `converted_to_type` | varchar(30) | ya | null | mis. `task`, `note`, `project` (polymorphic type informatif) |
| `converted_to_id` | char(26) ULID | ya | null | ID entitas hasil konversi |
| `processed_at` | timestamp | ya | null | |
| `deleted_at` | timestamp | ya | null | Soft delete |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (cascade: **restrict** — lihat Cascade Rules bagian B). **Index:** `(user_id, status)` composite (mendukung filter Inbox aktif). **Check Constraint:** `status IN ('unprocessed','processed','discarded')`. **Relationship:** belongsTo `User`; tidak ada relasi langsung ke Task/Note/Project di level foreign key (hanya referensi informatif `converted_to_type`/`converted_to_id`, **bukan** foreign key sungguhan — lihat alasan di Data Integrity Rules).

---

## A.3 Tabel `goals`

**Tujuan:** Tujuan jangka panjang, berujung atau berkelanjutan (FSD Modul 3.1).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `name` | varchar(255) | tidak | — | |
| `description` | text | ya | null | |
| `type` | varchar(20) enum | tidak | — | `ended` \| `ongoing` — **immutable setelah dibuat** |
| `completion_criteria` | text | ya | null | Wajib diisi jika `type = 'ended'` (divalidasi di level aplikasi, bukan check constraint DB — lihat catatan bagian B) |
| `status` | varchar(20) enum | tidak | `'active'` | `active` \| `completed` \| `abandoned` \| `paused` (kombinasi tergantung `type`, lihat Business Rules) |
| `deleted_at` | timestamp | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (restrict). **Index:** `(user_id, status)`, `(user_id, type)`. **Check Constraint:** `type IN ('ended','ongoing')`. **Relationship:** belongsTo `User`; hasMany `Project` (opsional, nullable FK di sisi `projects`).

---

## A.4 Tabel `projects`

**Tujuan:** Unit kerja menengah yang menaungi Task (FSD Modul 3.2).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `goal_id` | char(26) ULID | **ya** | null | FK → `goals.id`, opsional (Blueprint bagian 8: relasi nullable) |
| `name` | varchar(255) | tidak | — | |
| `description` | text | ya | null | |
| `status` | varchar(20) enum | tidak | `'active'` | `active` \| `completed` \| `archived` |
| `deleted_at` | timestamp | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (restrict); `goal_id → goals.id` (**set null on delete** — lihat FSD 3.2 Edge Case: Goal dihapus, Project tetap ada). **Index:** `(user_id, status)`, `(goal_id)`. **Check Constraint:** `status IN ('active','completed','archived')`. **Relationship:** belongsTo `User`, belongsTo `Goal` (nullable), hasMany `Task`, hasMany `Note` (nullable FK di sisi `notes`).

---

## A.5 Tabel `tasks`

**Tujuan:** Unit eksekusi harian (FSD Modul 2).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `project_id` | char(26) ULID | **ya** | null | FK → `projects.id`, opsional |
| `title` | varchar(255) | tidak | — | |
| `description` | text | ya | null | |
| `status` | varchar(20) enum | tidak | `'todo'` | `todo` \| `in_progress` \| `done` \| `archived` |
| `priority` | varchar(10) enum | tidak | `'medium'` | `low` \| `medium` \| `high` |
| `due_date` | date | ya | null | Boleh di masa lalu (pencatatan retroaktif — FSD 2.1) |
| `completed_at` | timestamp | ya | null | Diisi otomatis saat status → `done` |
| `deleted_at` | timestamp | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (restrict); `project_id → projects.id` (**set null on delete** — Task tidak ikut terhapus jika Project dihapus, konsisten dengan relasi opsional). **Index:** composite `(user_id, status, due_date)` (mendukung query Dashboard — TDD bagian 28), `(project_id)`. **Check Constraint:** `status IN ('todo','in_progress','done','archived')`, `priority IN ('low','medium','high')`. **Relationship:** belongsTo `User`, belongsTo `Project` (nullable), morphToMany `Tag`.

---

## A.6 Tabel `habits`

**Tujuan:** Definisi kebiasaan berulang (FSD Modul 7.1).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `name` | varchar(255) | tidak | — | |
| `frequency_type` | varchar(20) enum | tidak | — | `daily` \| `n_per_week` \| `weekly` |
| `frequency_target` | smallint | ya | null | Wajib diisi jika `frequency_type = 'n_per_week'`, nilai 1–7 |
| `status` | varchar(20) enum | tidak | `'active'` | `active` \| `paused` \| `archived` |
| `deleted_at` | timestamp | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (restrict). **Index:** `(user_id, status)`. **Check Constraint:** `frequency_type IN ('daily','n_per_week','weekly')`, `frequency_target BETWEEN 1 AND 7` (nullable check — hanya berlaku saat kolom terisi). **Relationship:** belongsTo `User`, hasMany `HabitLog`, morphToMany `Tag`.

---

## A.7 Tabel `habit_logs`

**Tujuan:** Catatan check-in harian per Habit (FSD Modul 7.2) — bersifat **append-only log**, bukan entitas yang diedit.

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `habit_id` | char(26) ULID | tidak | — | FK → `habits.id` |
| `logged_date` | date | tidak | — | Tanggal check-in (bukan `created_at`, agar retroaktif tetap akurat — FSD 7.2) |
| `created_at` | timestamp | tidak | now() | Tidak ada `updated_at` — log tidak pernah diedit |

**Foreign Key:** `habit_id → habits.id` (**cascade on delete** — jika Habit dihapus permanen, log ikut terhapus karena log tidak punya makna berdiri sendiri tanpa Habit induknya). **Unique Constraint:** `(habit_id, logged_date)` — mencegah check-in ganda di tanggal yang sama (FSD 7.2 Business Rule). **Index:** `(habit_id, logged_date)` (index yang sama menopang unique constraint sekaligus mempercepat query streak). **Soft Delete:** **tidak diterapkan** — log historis tidak dihapus lunak, hanya dihapus permanen mengikuti cascade Habit induknya (menjaga integritas perhitungan streak apa adanya).

---

## A.8 Tabel `notes`

**Tujuan:** Catatan/referensi Knowledge Base (FSD Modul 8).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `project_id` | char(26) ULID | **ya** | null | FK → `projects.id`, opsional, **satu Note satu Project** (FSD 8.2) |
| `title` | varchar(255) | tidak | — | |
| `content` | text | ya | null | Boleh kosong saat dibuat (FSD 8.1) |
| `status` | varchar(20) enum | tidak | `'active'` | `active` \| `archived` |
| `deleted_at` | timestamp | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (restrict); `project_id → projects.id` (**set null on delete** — FSD 8.2 Edge Case eksplisit). **Index:** `(user_id, status)`, `(project_id)`. **Check Constraint:** `status IN ('active','archived')`. **Relationship:** belongsTo `User`, belongsTo `Project` (nullable), morphToMany `Tag`.

---

## A.9 Tabel `tags` & `taggables` (Pivot Polymorphic)

**Tujuan:** Lapisan metadata lintas modul (FSD Modul 4).

**Tabel `tags`:**

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `name` | varchar(50) | tidak | — | Disimpan dalam bentuk normalisasi lowercase; nama tampilan asli disimpan terpisah jika diperlukan casing (Future Enhancement) |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Unique Constraint:** `(user_id, name)` — mencegah duplikat tag per user (FSD 4.1). **Foreign Key:** `user_id → users.id` (cascade — jika user dihapus, tag ikut terhapus; relevan hanya di skenario multi-user masa depan).

**Tabel `taggables` (pivot polymorphic):**

| Kolom | Tipe | Nullable | Keterangan |
|---|---|---|---|
| `tag_id` | char(26) ULID | tidak | FK → `tags.id` |
| `taggable_id` | char(26) ULID | tidak | ID entitas (Task/Project/Note/Habit) |
| `taggable_type` | varchar(50) | tidak | Nama Model (mis. `Task`, `Project`, `Note`, `Habit`) |

**Foreign Key:** `tag_id → tags.id` (**cascade on delete** — FSD 4.1: hapus Tag memutus seluruh relasinya). **Index:** `(taggable_type, taggable_id)`, `(tag_id)`. **Unique Constraint:** `(tag_id, taggable_id, taggable_type)` — mencegah tag yang sama terpasang dobel di entitas yang sama. **Soft Delete:** tidak berlaku (tabel pivot murni).

---

## A.10 Tabel `review_entries`

**Tujuan:** Ritual refleksi periodik dengan snapshot metrik yang dibekukan (FSD Modul 10).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `period_type` | varchar(10) enum | tidak | — | `daily` \| `weekly` \| `monthly` |
| `period_start_date` | date | tidak | — | Awal periode yang direview |
| `snapshot_metrics` | jsonb | tidak | `'{}'` | Data agregat beku (jumlah task selesai, streak habit, dll — FSD 10.2: **dibekukan**, tidak dihitung ulang otomatis) |
| `reflection_note` | text | ya | null | Opsional (FSD 10.1) |
| `deleted_at` | timestamp | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (restrict). **Unique Constraint:** `(user_id, period_type, period_start_date)` — satu entri per periode per user (FSD 10.1 Business Rule). **Index:** `(user_id, period_type, period_start_date)`. **Check Constraint:** `period_type IN ('daily','weekly','monthly')`. **Kolom `jsonb` (bukan `json`)** dipilih karena PostgreSQL `jsonb` mendukung index dan query lebih efisien dibanding `json` teks murni — relevan untuk Future Enhancement analitik tren (Blueprint bagian 14).

---

## A.11 Tabel `reminders`

**Tujuan:** Jadwal & riwayat pengiriman reminder, melayani baik Deadline Reminder (FSD Modul 6) maupun Full Notification Engine (FSD Modul 11) — satu tabel bersama, dibedakan lewat `reminder_type`, untuk menghindari duplikasi skema antar dua modul yang secara konsep serupa (TDD bagian 14: Notification Architecture).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id` |
| `remindable_id` | char(26) ULID | tidak | — | ID entitas sumber (polymorphic) |
| `remindable_type` | varchar(50) | tidak | — | `Task`, `Project`, `Habit`, atau `ReviewCycle` (konseptual, bukan tabel tersendiri) |
| `reminder_type` | varchar(20) enum | tidak | — | `deadline` \| `habit_schedule` \| `review_ritual` |
| `scheduled_at` | timestamp | tidak | — | Waktu reminder seharusnya terkirim |
| `status` | varchar(20) enum | tidak | `'scheduled'` | `scheduled` \| `sent` \| `cancelled` \| `skipped` |
| `sent_at` | timestamp | ya | null | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (cascade). **Index:** `(remindable_type, remindable_id)`, `(status, scheduled_at)` (mendukung scanner job harian — TDD bagian 15). **Check Constraint:** `reminder_type IN ('deadline','habit_schedule','review_ritual')`, `status IN ('scheduled','sent','cancelled','skipped')`. **Soft Delete:** tidak diterapkan — reminder yang sudah `cancelled`/`skipped` cukup ditandai statusnya, tidak perlu dihapus lunak.

---

## A.12 Tabel `notification_preferences`

**Tujuan:** Preferensi reminder terpusat, dibaca bersama oleh Modul Deadline Reminder dan Full Notification Engine (klarifikasi FSD, Catatan Konsistensi awal dokumen).

| Kolom | Tipe | Nullable | Default | Keterangan |
|---|---|---|---|---|
| `id` | char(26) ULID | tidak | — | Primary Key |
| `user_id` | char(26) ULID | tidak | — | FK → `users.id`, **unique** (satu preferensi per user) |
| `deadline_reminder_enabled` | boolean | tidak | `true` | |
| `habit_reminder_enabled` | boolean | tidak | `true` | |
| `habit_reminder_time` | time | tidak | `'20:00'` | |
| `review_ritual_enabled` | boolean | tidak | `true` | |
| `created_at` / `updated_at` | timestamp | tidak | now() | |

**Foreign Key:** `user_id → users.id` (cascade). **Unique Constraint:** `user_id` (relasi one-to-one). **Soft Delete:** tidak diterapkan (baris ini selalu ada selama user ada, dibuat otomatis via default saat registrasi).

---

# BAGIAN B — BUSINESS RULES PER ENTITAS

## B.1 Task

- **Lifecycle:** `todo → in_progress → done → (reopen) → todo`; `todo/in_progress → archived` (final).
- **Allowed State Transition:** `todo↔in_progress`, `todo/in_progress→done`, `done→todo` (reopen), `todo/in_progress→archived`.
- **Invalid State:** `archived → done` langsung (harus dibuka lagi ke `todo`/`in_progress` dulu — mencegah Task "dibatalkan" tiba-tiba dianggap "selesai" tanpa proses eksplisit); `done → archived` langsung juga dianggap tidak valid (Task yang sudah selesai tidak perlu diarsipkan, cukup dibiarkan `done`).
- **Validation:** `title` wajib 1–255 karakter; `due_date` bebas termasuk masa lalu.
- **Reminder Rules:** Reminder otomatis dijadwalkan (`reminders.reminder_type = 'deadline'`) saat `due_date` diisi; dibatalkan otomatis saat status → `done`/`archived`.
- **Repeat Rules:** Task **tidak mendukung** pengulangan otomatis pada MVP (recurring task adalah Future Enhancement FSD 2.1) — setiap Task adalah instance tunggal.
- **Priority Rules:** Default `medium`; memengaruhi urutan tampilan Dashboard, tidak memengaruhi validasi/lifecycle.
- **Completion Rules:** Menandai `done` mengisi `completed_at`; memicu Event `TaskCompleted` (TDD bagian 13).
- **Archive Rules:** `archived` tidak dihitung dalam progres Project (berbeda dari `done`).

## B.2 Project

- **Lifecycle:** `active → completed` (manual, dikonfirmasi user, bukan otomatis 100%); `active → archived`.
- **Allowed State:** `active↔archived` (dapat dibuka kembali), `active→completed`.
- **Invalid State:** `completed → active` **tanpa aksi eksplisit** dianggap tidak wajar tapi **diperbolehkan secara teknis** (user dapat membuka kembali Project yang sudah selesai jika ternyata ada Task tambahan) — dicatat sebagai transisi valid namun tidak umum.
- **Validation:** `name` wajib; `goal_id` jika diisi harus milik user yang sama.
- **Reminder Rules:** Tidak langsung — mengikuti Task di dalamnya.
- **Repeat Rules:** Tidak berlaku pada MVP (template Project adalah Future Enhancement FSD 3.2).
- **Priority Rules:** Tidak ada atribut prioritas eksplisit di level Project pada MVP.
- **Completion Rules:** Progres dihitung otomatis dari rasio Task `done` terhadap Task aktif (tidak termasuk `archived`); tidak dapat diisi manual.
- **Archive Rules:** Task di bawah Project yang `archived` tetap dapat diselesaikan (FSD 2.2 Edge Case), namun tidak memengaruhi progres yang sudah non-aktif.

## B.3 Goal

- **Lifecycle (berujung):** `active → completed` / `active → abandoned`.
- **Lifecycle (berkelanjutan):** `active ↔ paused` (tidak ada `completed`).
- **Invalid State:** Goal berkelanjutan **tidak boleh** berstatus `completed` — divalidasi di level aplikasi (Action) berdasarkan `type`, bukan hanya check constraint database sederhana, karena aturan valid-tidaknya status bergantung nilai kolom lain (`type`).
- **Validation:** `type` **immutable** setelah dibuat (FSD 3.1); jika `type = 'ended'`, `completion_criteria` wajib diisi.
- **Reminder Rules:** Tidak langsung.
- **Repeat Rules:** Tidak berlaku.
- **Priority Rules:** Tidak ada di MVP.
- **Completion Rules:** Untuk Goal berujung, `completed` ditandai manual oleh user berdasarkan `completion_criteria` (teks bebas, bukan dihitung otomatis — FSD 3.1).
- **Archive Rules:** Goal tanpa Project (belum ada aktivitas) ditampilkan sebagai "belum ada aktivitas", bukan progres 0% yang menyesatkan.

## B.4 Habit

- **Lifecycle:** `active ↔ paused → archived` (final, tidak kembali ke `active`).
- **Invalid State:** `archived → active` **tidak diperbolehkan** — jika user ingin melanjutkan kebiasaan yang sama, direkomendasikan membuat Habit baru agar riwayat streak lama tidak tercampur ambigu dengan periode setelah diarsipkan.
- **Validation:** `frequency_target` wajib 1–7 jika `frequency_type = 'n_per_week'`; perubahan frekuensi **tidak menghitung ulang streak lama secara retroaktif** (FSD 7.1).
- **Reminder Rules:** Sumber jadwal untuk `reminders.reminder_type = 'habit_schedule'` (Full Notification Engine).
- **Repeat Rules:** Inilah inti modul — pengulangan didefinisikan lewat `frequency_type`/`frequency_target`, dieksekusi lewat `habit_logs`.
- **Priority Rules:** Tidak berlaku.
- **Completion Rules:** Tidak ada "selesai" final untuk Habit — hanya `paused`/`archived` sebagai penghentian.
- **Archive Rules:** Riwayat `habit_logs` tetap tersimpan permanen (cascade hanya terjadi jika Habit benar-benar dihapus, bukan diarsipkan — arsip mempertahankan seluruh riwayat).

## B.5 Note

- **Lifecycle:** `active ↔ archived` (dapat dibuka kembali kapan saja).
- **Invalid State:** Tidak ada — Note adalah entitas paling sederhana lifecycle-nya (dua status yang bebas bolak-balik).
- **Validation:** `title` wajib; `content` boleh kosong.
- **Reminder Rules:** Tidak berlaku.
- **Repeat Rules:** Tidak berlaku.
- **Priority Rules:** Tidak berlaku.
- **Completion Rules:** Tidak berlaku (Note bukan Task, tidak ada konsep selesai — FSD 8.1).
- **Archive Rules:** Note `archived` tetap dapat dicari (FSD 8.1), hanya disembunyikan dari daftar aktif default.

## B.6 InboxItem

- **Lifecycle:** `unprocessed → processed` / `unprocessed → discarded` (keduanya final).
- **Invalid State:** `processed/discarded → unprocessed` **tidak diperbolehkan** — item yang sudah ditriase bersifat read-only arsip (FSD 1.2).
- **Validation:** `content` 1–5000 karakter.
- **Reminder Rules:** Tidak ada di MVP (reminder Inbox menumpuk adalah Future Enhancement FSD 1.2).
- **Repeat Rules:** Tidak berlaku.
- **Priority Rules:** Tidak berlaku.
- **Completion Rules:** "Selesai" dalam konteks Inbox berarti `processed` (berhasil dikonversi).
- **Archive Rules:** Item `processed`/`discarded` disembunyikan dari tampilan Inbox aktif namun tidak dihapus dari database (soft delete tetap tersedia untuk kasus penghapusan eksplisit terpisah dari status `discarded`).

## B.7 Tag

- **Lifecycle:** Tidak bertingkat — hanya ada/tidak ada.
- **Validation:** `name` unik per user (case-insensitive — dinormalisasi lowercase saat disimpan), 1–50 karakter.
- **Completion/Archive Rules:** Tidak berlaku.

## B.8 ReviewEntry

- **Lifecycle:** Tidak bertingkat — dapat diedit (`reflection_note`) kapan saja setelah dibuat, namun `snapshot_metrics` **tidak berubah otomatis** setelah dibekukan (FSD 10.2).
- **Validation:** Satu entri per `(user_id, period_type, period_start_date)`.
- **Completion Rules:** Tidak berlaku — Review tidak punya status selesai/belum, hanya ada/tidak ada per periode.

---

# BAGIAN C — STATE MACHINE DIAGRAM (DESKRIPTIF)

```
TASK:        todo ⇄ in_progress → done ⇄(reopen) todo
                  todo/in_progress → archived (final)

PROJECT:     active ⇄ archived
             active → completed (manual, dapat kembali ke active jika perlu)

GOAL (ended):      active → completed | active → abandoned
GOAL (ongoing):    active ⇄ paused

HABIT:       active ⇄ paused → archived (final, tidak kembali)

NOTE:        active ⇄ archived (bebas bolak-balik)

INBOX ITEM:  unprocessed → processed (final)
             unprocessed → discarded (final)

REMINDER:    scheduled → sent (final)
             scheduled → cancelled | scheduled → skipped (final)
```

---

# BAGIAN D — ENTITY LIFECYCLE (RINGKASAN LINTAS ENTITAS)

`InboxItem` (transien) → melahirkan → `Task`/`Note`/`Project` (permanen) → `Task` menjadi bagian `Project` (opsional) → `Project` menjadi bagian `Goal` (opsional) → penyelesaian `Task` memicu recalculation progres `Project` dan `Goal` secara berantai → seluruh aktivitas terekam sebagai data mentah yang diagregasi menjadi snapshot beku di `ReviewEntry` secara periodik. `Habit` berjalan paralel dan independen dari rantai Goal→Project→Task, dengan siklus hidupnya sendiri berbasis `HabitLog` harian.

---

# BAGIAN E — DATA INTEGRITY RULES

1. **Isolasi data per user**: setiap query terhadap tabel manapun **wajib** difilter `user_id = auth()->id()` di level Policy/global scope Eloquent — tidak boleh mengandalkan hanya foreign key untuk isolasi (Blueprint bagian 19).
2. **`InboxItem.converted_to_id`/`converted_to_type` sengaja bukan foreign key sungguhan** — karena entitas tujuan (Task/Note/Project) dapat dihapus sepenuhnya di kemudian hari tanpa perlu memutus/membatalkan integritas arsip historis `InboxItem`; validasi keberadaan dilakukan di level aplikasi saat pembacaan riwayat, bukan dipaksakan di level database.
3. **Tipe Goal (`type`) bersifat immutable** — ditegakkan di level Action (`UpdateGoalAction` menolak perubahan field ini), bukan di level database, karena PostgreSQL tidak memiliki mekanisme native "kolom hanya bisa diisi sekali".
4. **Cascade rules dirancang asimetris secara sengaja**: entitas yang **memiliki makna berdiri sendiri** (Task, Note) menggunakan `set null` saat induknya (Project) dihapus — sedangkan entitas **log murni tanpa makna berdiri sendiri** (`habit_logs`, pivot `taggables`) menggunakan `cascade` — perbedaan ini bukan kelalaian, melainkan cerminan langsung dari perbedaan sifat data di Blueprint dan FSD.
5. **`snapshot_metrics` pada `review_entries` adalah data yang sengaja terduplikasi** dari sumber lain (Task/Habit) — ini adalah pengecualian sadar terhadap normalisasi murni, karena tujuannya adalah membekukan kondisi historis, bukan mencerminkan kondisi terkini.

---

# BAGIAN F — EDGE CASES (RINGKASAN LINTAS TABEL)

- Project dihapus sementara Task di bawahnya masih `todo` → Task menjadi berdiri bebas (`project_id = null`), tetap dapat dikerjakan dan diselesaikan.
- Goal dihapus sementara Project di bawahnya `active` → Project menjadi berdiri bebas (`goal_id = null`), progres Project tidak terpengaruh.
- Tag dihapus sementara dipakai puluhan entitas → seluruh baris `taggables` terkait terhapus cascade, entitas asal (Task/Project/Note/Habit) tidak terpengaruh selain kehilangan satu tag.
- Habit diarsipkan (bukan dihapus) → `habit_logs` tetap utuh selamanya; hanya dihapus jika Habit **dihapus permanen** (bukan sekadar `archived`).
- Dua reminder untuk `remindable` yang sama pada waktu berdekatan (mis. deadline diubah dua kali berturut-turut) → reminder lama otomatis `cancelled` sebelum reminder baru dibuat, mencegah duplikasi pengiriman.

---

# BAGIAN G — MIGRATION ORDER

Urutan migration wajib mengikuti dependency foreign key (dan selaras dengan Module Dependency di Blueprint bagian 8 & TDD bagian 12.2):

```
1.  users
2.  tags
3.  goals              (FK → users)
4.  projects           (FK → users, goals)
5.  tasks              (FK → users, projects)
6.  habits             (FK → users)
7.  habit_logs         (FK → habits)
8.  notes              (FK → users, projects)
9.  taggables          (FK → tags; polymorphic ke tasks/projects/notes/habits)
10. review_entries     (FK → users)
11. notification_preferences (FK → users)
12. reminders          (FK → users; polymorphic ke tasks/projects/habits)
```

**Alasan `tags` diletakkan di urutan awal (posisi 2):** meski secara fungsional tampak "pendukung", `taggables` bergantung padanya dan pada seluruh entitas taggable lain — menempatkan `tags` lebih awal (setelah `users`) menghindari perlunya migration terpisah untuk menambahkan constraint belakangan.

---

# BAGIAN H — SEEDER STRATEGY

1. **`UserSeeder`**: membuat satu akun user default untuk kebutuhan development lokal (single-user, sesuai konteks aplikasi saat ini).
2. **`NotificationPreferenceSeeder`**: otomatis membuat satu baris default per user yang dibuat (idealnya dipicu via Model Observer `created` pada `User`, bukan seeder terpisah yang mudah terlupa saat registrasi user baru di masa depan).
3. **`TagSeeder`** (opsional, untuk development): beberapa tag umum berdasarkan aktivitas yang disebut di Blueprint bagian 3.4 (mis. "YouTube", "Belajar Jepang") agar tampilan awal tidak kosong sama sekali saat development UI.
4. Seeder **tidak** mengisi data Task/Project/Goal/Habit secara default — data tersebut sepenuhnya milik **Factory** untuk kebutuhan testing (lihat Bagian I), sedangkan seeder production hanya untuk data yang benar-benar wajib ada (user & preference).

---

# BAGIAN I — FACTORY STRATEGY

Setiap Model utama (`Task`, `Project`, `Goal`, `Habit`, `Note`, `InboxItem`, `ReviewEntry`) memiliki Factory Laravel standar, dengan dua pertimbangan desain:

1. **State method eksplisit per status** (mis. `Task::factory()->done()`, `Task::factory()->archived()`, `Goal::factory()->ongoing()`) — bukan hanya factory generik dengan status acak — agar Feature Test (TDD bagian 34) dapat membuat data uji dengan kondisi state machine yang presisi tanpa perlu manipulasi manual setelah instansiasi.
2. **Relasi opsional dibuat eksplisit lewat method**, bukan otomatis (mis. `Task::factory()->for(Project::factory())`) — agar Factory default menghasilkan Task/Note **berdiri bebas** (tanpa Project), mencerminkan fleksibilitas struktur data yang justru menjadi prinsip inti Blueprint — bukan selalu mengasumsikan relasi lengkap seperti aplikasi konvensional.

`HabitLogFactory` khusus menyediakan state `forConsecutiveDays(int $n)` untuk mempermudah pengujian logika streak (`RecalculateHabitStreakAction`) tanpa menulis ulang loop tanggal manual di setiap test.

---

# BAGIAN J — DATABASE OPTIMIZATION

1. Composite index `(user_id, status, due_date)` pada `tasks` — menopang query Dashboard yang paling sering dijalankan (TDD bagian 28).
2. Composite index `(status, scheduled_at)` pada `reminders` — menopang scanner job harian (Bagian A.11) agar tidak full table scan setiap kali dijalankan.
3. **Partial index** PostgreSQL pada `tasks` untuk `WHERE status NOT IN ('done','archived')` — mempercepat query "Task aktif" yang jauh lebih sering diakses dibanding Task yang sudah selesai/diarsipkan, tanpa membengkakkan ukuran index dengan baris yang jarang di-query.
4. `jsonb` (bukan `json`) pada `review_entries.snapshot_metrics` mendukung index GIN di masa depan jika analitik tren (Future Enhancement) membutuhkan query terhadap isi JSON.
5. Foreign key `user_id` di seluruh tabel di-index secara eksplisit (bukan mengandalkan index implisit dari constraint saja) untuk menjamin performa filter kepemilikan yang terjadi di **hampir setiap query aplikasi ini**.

---

# BAGIAN K — FUTURE DATABASE SCALABILITY

1. **Transisi single-user → multi-user**: tidak memerlukan migrasi skema (`user_id` sudah ada di mana pun) — hanya memerlukan penambahan alur registrasi dan (jika diperlukan) tabel `roles`/`permissions` terpisah yang terhubung ke `users` tanpa mengubah tabel yang sudah ada (Blueprint bagian 19).
2. **Kolaborasi Project lintas user** (jika direalisasikan — Blueprint bagian 19, poin 3): dapat ditambahkan lewat tabel pivot baru `project_collaborators (project_id, user_id, role)`, tanpa mengubah struktur `projects` maupun `tasks` yang sudah ada — Task tetap mempertahankan satu `user_id` sebagai pemilik utama.
3. **Read-replica** (Blueprint bagian 22): seluruh query Dashboard dan Search bersifat read-heavy dan tidak bergantung pada data yang baru saja ditulis dalam milidetik yang sama — aman diarahkan ke replica saat traffic meningkat, tanpa perubahan skema.
4. **Full-text search PostgreSQL** (Future Enhancement FSD Modul 12): dapat ditambahkan lewat kolom `tsvector` ter-generate pada `tasks.title+description`, `notes.title+content` beserta index GIN, tanpa mengubah struktur kolom yang sudah ada — murni penambahan kolom turunan.
5. **Partisi tabel `habit_logs`/`reminders`** (jika volume data bertambah signifikan di rentang bertahun-tahun): dapat dipartisi berdasarkan rentang tanggal (`logged_date`/`scheduled_at`) tanpa mengubah struktur kolom, hanya strategi penyimpanan fisik PostgreSQL — relevan hanya jika skala penggunaan bertahun-tahun benar-benar tercapai (Blueprint bagian 14, Tahun 3–5).

---

## Penutup

Dokumen ini melengkapi TDD dengan spesifikasi schema yang presisi — 12 tabel dengan seluruh kolom, tipe data, constraint, dan aturan bisnis yang selaras dengan setiap fitur di FSD. Developer dapat langsung menulis file migration Laravel mengikuti urutan di Bagian G, membuat Model dengan relasi sesuai Bagian A, dan menerapkan aturan bisnis di Action class sesuai Bagian B — tanpa perlu mengambil keputusan skema tambahan di tengah proses implementasi.
