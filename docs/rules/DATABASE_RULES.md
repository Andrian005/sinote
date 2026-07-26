# DATABASE_RULES.md

*(Ringkasan operasional dari Database & Business Rules Specification — untuk skema kolom lengkap per tabel, baca dokumen asli di `docs/context/reference/04-database-business-rules.md`)*

## Aturan Wajib untuk Setiap Migration Baru

1. **Primary key**: `id` bertipe `char(26)` ULID — tidak ada auto-increment integer.
2. **`user_id`**: wajib ada di setiap tabel entitas utama, FK ke `users.id`.
3. **Soft delete**: `deleted_at` wajib di setiap tabel entitas utama (kecuali tabel log murni seperti `habit_logs` dan tabel pivot).
4. **Timestamp**: `created_at`+`updated_at` standar, kecuali tabel append-only log yang hanya punya `created_at`.
5. **Check constraint** untuk kolom enum-string di level database, selaras dengan backed Enum PHP di kode.

## Migration Order (Tidak Boleh Diubah Urutannya)

```
1. users            6. habits
2. tags             7. habit_logs
3. goals            8. notes
4. projects         9. taggables
5. tasks            10. review_entries
                     11. notification_preferences
                     12. reminders
```

## Cascade Rules — Prinsip Asimetris

- **`set null on delete`**: dipakai saat entitas anak punya makna berdiri sendiri tanpa induknya (`tasks.project_id`, `projects.goal_id`, `notes.project_id`).
- **`cascade on delete`**: dipakai saat entitas anak murni log/relasi tanpa makna berdiri sendiri (`habit_logs.habit_id`, `taggables.tag_id`).
- **`restrict`**: dipakai untuk `user_id` di seluruh tabel (user tidak dihapus sembarangan pada MVP).

Jangan menentukan cascade rule baru tanpa memeriksa dulu apakah entitas tersebut "bermakna berdiri sendiri" atau "murni log" — ini menentukan pilihan yang benar (lihat Database Spec Bagian E, Data Integrity Rules #4).

## Aturan Khusus

- `InboxItem.converted_to_id`/`converted_to_type` **bukan** foreign key sungguhan — jangan tambahkan constraint FK untuk kolom ini.
- `Goal.type` (`ended`/`ongoing`) **immutable** setelah dibuat — validasi ini di level Action, bukan hanya di form.
- `review_entries.snapshot_metrics` **tidak boleh** dihitung ulang otomatis setelah dibuat — sekali dibekukan, tetap seperti itu.
- `reminders` adalah **satu tabel bersama** untuk Deadline Reminder dan Full Notification Engine (dibedakan `reminder_type`) — jangan buat tabel reminder terpisah per modul.

## Optimasi Wajib Sejak Awal

- Composite index `(user_id, status, due_date)` pada `tasks`.
- Composite index `(status, scheduled_at)` pada `reminders`.
- Index `user_id` eksplisit di seluruh tabel (jangan andalkan index implisit dari FK constraint saja).

## Checklist Sebelum Migration Dianggap Selesai

- [ ] ULID PK, `user_id`, soft delete, timestamp sesuai aturan di atas.
- [ ] Cascade rule dipilih sesuai prinsip asimetris (bukan default framework begitu saja).
- [ ] Index yang disebut di Database Spec Bagian J sudah ditambahkan.
- [ ] Check constraint status selaras dengan Enum PHP terkait.
