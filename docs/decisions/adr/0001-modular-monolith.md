# ADR-0001: Modular Monolith, Bukan Microservices

- **Status:** Accepted
- **Tanggal:** Ditetapkan pada tahap Technical Architecture (TDD bagian 2)
- **Sumber terkait:** Blueprint v1.0 bagian 9, TDD bagian 2–3

## Context

Aplikasi memiliki 12 modul fitur yang cukup terpisah secara konsep (Inbox, Tasks, Projects & Goals, Habit, dst.), dikembangkan dan dirawat oleh **satu orang**. Perlu diputuskan apakah setiap modul dipisah menjadi service/deployment independen (microservices) atau tetap dalam satu aplikasi (monolith), dan jika monolith, bagaimana batas antar modul dijaga.

## Decision

Aplikasi dibangun sebagai **modular monolith**: satu aplikasi Laravel, satu deployment, dengan setiap modul FSD dipetakan menjadi domain folder mandiri di `app/Domain/{Modul}/`. Batas antar modul dijaga lewat konvensi (Action tidak mengakses Model modul lain langsung) dan Event/Listener untuk efek lintas modul, bukan lewat pemisahan jaringan/proses.

## Alternatives Considered

- **Microservices** — ditolak. Menambah kompleksitas operasional (banyak deployment, orkestrasi, titik kegagalan jaringan) yang tidak sepadan untuk solo developer dan skala pengguna saat ini (single-user).
- **Monolith tanpa struktur domain (flat MVC konvensional)** — ditolak. Tanpa batas modul yang jelas, 12 modul fitur berisiko saling bercampur logikanya seiring waktu, menyulitkan solo developer menelusuri kode setelah jeda panjang.

## Consequences

- **Positif:** deployment sederhana (satu aplikasi), batas modul tetap terjaga secara disiplin lewat struktur folder dan Event/Listener, mudah dirawat solo developer.
- **Negatif/Trade-off:** disiplin manual diperlukan untuk menjaga batas modul (tidak ada enforcement otomatis dari framework/infrastruktur seperti pada microservices sungguhan).

## Kapan Ditinjau Ulang

Jika salah satu modul (mis. Notification Engine) menjadi bottleneck performa yang terukur nyata pada monolith, modul tersebut dapat dipecah menjadi service terpisah — opsi ini sudah tersedia karena batas domain sudah dijaga sejak awal, bukan kebutuhan mendesak saat ini.
