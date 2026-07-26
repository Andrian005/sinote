# REFACTOR-0001: [Contoh Template] Pecah Action yang Menyerap Tanggung Jawab Modul Lain

> **Ini adalah tiket contoh/template** — dipakai saat Refactoring Strategy (Implementation Guide bagian 12) mengidentifikasi satu Action/Model mulai menyerap tanggung jawab di luar domainnya.

- **ID:** REFACTOR-0001
- **Judul:** Pecah Action yang menyerap tanggung jawab lintas modul
- **Deskripsi:** *(Contoh)* Selama implementasi EPIC-004, ditemukan bahwa `CompleteTaskAction` (domain Tasks) mulai langsung memanggil logika perhitungan progres Project (domain Projects) secara langsung, alih-alih lewat Event `TaskCompleted` + Listener terpisah — melanggar `ARCHITECTURE_RULES.md` § 7 (Event/Listener untuk efek lintas modul).
- **Modul Terdampak:** Tasks, Projects & Goals.
- **Dependency:** Tidak ada — refactoring tidak mengubah perilaku eksternal (harus tetap lolos test yang sama sebelum dan sesudah).
- **Priority:** Sedang–Tinggi (pelanggaran ARCHITECTURE_RULES.md dapat menumpuk jika dibiarkan).
- **Estimasi:** 0.5–1 hari.
- **Status:** `Backlog` *(contoh)*

## Kondisi Sebelum Refactoring

`CompleteTaskAction::execute()` memanggil `RecalculateProjectProgressAction` secara langsung di dalam method-nya sendiri.

## Kondisi Sesudah Refactoring (Target)

`CompleteTaskAction::execute()` hanya mengubah status Task dan men-dispatch Event `TaskCompleted`. `RecalculateProjectProgressAction` dipanggil oleh Listener `RecalculateProjectProgress` yang bereaksi terhadap Event tersebut (sesuai Event & Listener Mapping, TDD bagian 13).

## Acceptance Criteria

- [ ] Seluruh test yang ada untuk `CompleteTaskAction` dan `RecalculateProjectProgressAction` tetap hijau **tanpa perubahan assertion** (membuktikan perilaku eksternal tidak berubah).
- [ ] `CompleteTaskAction` tidak lagi mengimpor/memanggil apa pun dari domain `Projects` secara langsung.
- [ ] Listener baru ditambahkan dan diuji terpisah.

## Checklist

- [ ] Refactoring dilakukan **setelah** modul terkait berstatus selesai (bukan di tengah penulisan fitur baru) — sesuai Refactoring Strategy.
- [ ] Tidak ada fitur baru ditambahkan dalam tiket ini (murni kualitas kode, bukan scope baru).
- [ ] Code Review Checklist dijalankan.
- [ ] `docs/tracking/CHANGELOG.md` dicatat (opsional untuk refactor murni internal, dicatat jika berdampak pada struktur yang perlu diketahui sesi berikutnya — juga catat di `docs/ai/AI_MEMORY.md` jika ada nuansa implementasi yang perlu diingat).
