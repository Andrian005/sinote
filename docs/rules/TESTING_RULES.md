# TESTING_RULES.md

*(Ringkasan operasional dari Implementation Guide bagian 10 dan TDD bagian 34)*

## Prioritas Testing (Bukan Cakupan 100%)

Testing difokuskan pada **area risiko tertinggi**, bukan menguji semua baris kode:

**Prioritas Tinggi (unit test wajib):**
- Transisi State Machine (Task, Project, Goal, Habit).
- Kalkulasi agregat (`RecalculateProjectProgressAction`, `RecalculateHabitStreakAction`).
- Snapshot freeze (`ReviewEntry.snapshot_metrics` tidak boleh berubah otomatis).
- Kondisi kirim/skip reminder (mencegah duplikasi).

**Prioritas Sedang (feature test):**
- Alur end-to-end per Use Case FSD (create→complete Task, capture→triage, dsb).
- CRUD dasar entitas.

**Prioritas Rendah (manual/dogfooding cukup):**
- Modul UI-murni tanpa logika bisnis kompleks (Focus Mode, styling Dashboard).

## Jenis Test

- **Unit test**: Action dan Enum, database SQLite in-memory untuk kecepatan (kecuali fitur spesifik PostgreSQL seperti jsonb/full-text search).
- **Feature test**: alur lewat route/Livewire component sungguhan.
- **Integration test**: melibatkan lebih dari satu Model/relasi (mis. Task+Project, Habit+HabitLog).
- **Manual testing**: dogfooding harian — dijalankan sejak modul pertama (Inbox) selesai.
- **Edge case testing**: wajib untuk setiap Edge Case yang tercatat eksplisit di FSD per fitur (jangan lewati — itulah alasan FSD mencatatnya).

## Aturan Wajib

1. Setiap Action baru dengan kalkulasi/transisi status **tidak boleh** disambungkan ke Livewire sebelum unit test-nya hijau.
2. Setiap tiket FEATURE/TASK yang menyentuh business rule harus menyertakan minimal satu edge case test dari FSD terkait.
3. Test suite dijalankan penuh sebelum setiap merge ke `main` (lewat CI).

## Checklist Sebelum Tiket Dianggap "Teruji"

- [ ] Unit test untuk Action berisiko tinggi — hijau.
- [ ] Feature test untuk alur utama tiket — hijau.
- [ ] Minimal satu edge case dari FSD terkait diuji eksplisit.
- [ ] Tidak ada test yang di-skip tanpa alasan tercatat.
