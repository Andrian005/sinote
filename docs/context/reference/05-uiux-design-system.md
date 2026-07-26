# PERSONAL OS — UI/UX SPECIFICATION & DESIGN SYSTEM
### Acuan Desain Figma & Implementasi Frontend — Laravel + Tailwind CSS + Livewire

---

## Prinsip Desain yang Mendasari Seluruh Dokumen

Mengacu pada Blueprint bagian 10 & 14 (UX Recommendation, Design Principle): **clarity over density**, **progressive disclosure**, **calm technology**, dan **konsistensi pola interaksi lintas modul**. Setiap keputusan UI di bawah ini — dari pemilihan komponen hingga radius sudut — dipandu oleh prinsip bahwa aplikasi ini adalah *thinking layer* harian, bukan dashboard analitik yang padat data.

---

# BAGIAN A — SPESIFIKASI PER HALAMAN

## A.1 Halaman: Today (Dashboard)

- **Tujuan Halaman:** Landing page default; menjawab "apa yang penting sekarang" (FSD Modul 5).
- **Layout:** Single-column pada mobile; two-column pada desktop (kolom utama: Task prioritas + reminder aktif; kolom samping: checklist Habit + snapshot progres Goal/Project).
- **Navigation:** Bagian dari navigasi primer (item pertama, selalu ter-highlight sebagai halaman aktif default).
- **Sidebar:** Persistent di desktop (≥1024px), collapsible menjadi bottom navigation di mobile (<768px).
- **Header:** Judul halaman "Today" + tanggal hari ini + search bar global + tombol Quick Capture mengambang (selalu terlihat, prioritas visual tertinggi di header).
- **Footer:** Tidak ada footer konten — aplikasi produktivitas harian tidak memerlukan footer informasional; ruang dioptimalkan untuk konten.
- **Widget:** Widget "Prioritas Hari Ini" (list Task), widget "Habit Hari Ini" (checklist ringkas), widget "Reminder Aktif" (badge count + list ringkas), widget "Snapshot Progres" (progress bar Goal/Project aktif).
- **Component:** Task item row (checkbox + judul + badge prioritas + badge Project), Habit checklist item, reminder banner.
- **Card:** Setiap widget dibungkus dalam Card dengan header ringkas (judul widget) dan area konten scrollable jika melebihi tinggi maksimum (mis. maksimal 5 item terlihat, sisanya "lihat semua").
- **Table:** Tidak digunakan di halaman ini — daftar disajikan sebagai list/card, bukan tabel data (menjaga kesan ringan, bukan spreadsheet).
- **Modal:** Modal "Tambah Task Cepat" dapat dipicu dari widget Prioritas Hari Ini tanpa berpindah halaman.
- **Form:** Tidak ada form penuh di halaman ini — hanya form mini di dalam modal.
- **Button:** Tombol Quick Capture (primary, mengambang/sticky); tombol "lihat semua" per widget (tertiary/link style).
- **Empty State:** Jika tidak ada Task/Habit relevan hari ini → ilustrasi ringan + teks positif (mis. "Tidak ada yang mendesak hari ini — waktu bagus untuk merencanakan ke depan"), bukan tampilan kosong yang terkesan seperti error (FSD 5.1).
- **Loading State:** Skeleton loader per widget (bukan spinner tunggal seluruh halaman) — widget yang datanya sudah siap tampil lebih dulu, tidak menunggu widget paling lambat (progressive rendering, Livewire `wire:loading` per komponen).
- **Error State:** Kegagalan satu widget menampilkan pesan error ringkas hanya di widget tersebut ("Gagal memuat Habit — coba muat ulang"), widget lain tetap berfungsi (FSD 5.1: kegagalan parsial).
- **Success State:** Tidak ada success state besar di halaman ini — feedback keberhasilan bersifat mikro (mis. checkbox Task langsung tercoret dengan animasi halus saat ditandai selesai).
- **Search:** Search bar global di header, mengarahkan ke Modul Search (bukan search lokal di Dashboard).
- **Filter:** Tidak ada filter manual pada MVP (Dashboard sengaja tidak dapat difilter agar tetap sebagai satu pandangan tunggal terkurasi — Future Enhancement Blueprint bagian 17 baru menambah kustomisasi).
- **Pagination:** Tidak berlaku — widget dibatasi jumlah item tampil dengan link "lihat semua" ke halaman penuh (Tasks/Habits), bukan pagination di dalam Dashboard.
- **Keyboard Shortcut:** `C` = buka Quick Capture dari mana pun; `G then T` = ke halaman Today (pola "go to", umum di aplikasi produktivitas).
- **Responsive Behavior:** Desktop: grid 2 kolom; Tablet: 1 kolom dengan widget disusun prioritas (Task dulu, lalu Habit, lalu Reminder); Mobile: 1 kolom penuh, widget dapat di-collapse.
- **Accessibility:** Checkbox Task dapat dioperasikan via keyboard (Tab + Space); kontras teks badge prioritas memenuhi WCAG AA minimum; ARIA live region untuk widget yang ter-update otomatis (mis. progress bar) agar pembaca layar mengumumkan perubahan.
- **Animation:** Transisi halus (150–200ms) saat Task berpindah status (checkbox tercentang → strikethrough fade); tidak ada animasi masuk-halaman yang berlebihan (menjaga performa dan prinsip calm technology).
- **Interaction:** Klik judul Task pada Dashboard membuka detail singkat (popover), bukan langsung berpindah halaman penuh — meminimalkan konteks yang hilang.
- **UX Rules:** Maksimal 5–7 item per widget list secara default; tidak pernah menampilkan seluruh Task mentah di halaman ini (prinsip inti FSD 5.1).

## A.2 Halaman: Inbox

- **Tujuan Halaman:** Capture cepat dan triase (FSD Modul 1).
- **Layout:** Single-column list penuh — didesain untuk kecepatan proses linear (item demi item), bukan grid.
- **Navigation:** Item navigasi primer kedua.
- **Sidebar:** Sama seperti A.1.
- **Header:** Judul "Inbox" + counter jumlah item belum diproses + input Quick Capture besar selalu di posisi teratas (bukan tersembunyi di modal — Inbox adalah tempat paling natural untuk capture penuh, bukan hanya shortcut).
- **Footer:** Tidak ada.
- **Widget:** Tidak berlaku (halaman ini adalah satu daftar kerja, bukan agregasi berbagai sumber).
- **Component:** Input capture besar (textarea auto-expand); item row dengan 4 tombol aksi cepat (Task/Note/Project/Hapus) muncul on-hover (desktop) atau selalu terlihat sebagai icon (mobile).
- **Card:** Setiap item Inbox adalah Card tipis (low elevation) untuk membedakannya dari list Task yang lebih terstruktur.
- **Table:** Tidak digunakan.
- **Modal:** Modal "Masukkan ke Project" muncul saat aksi tersebut dipilih, meminta memilih/membuat Project tujuan.
- **Form:** Form capture di header (single textarea + tombol submit); tidak ada form kompleks lain di halaman utama.
- **Button:** 4 tombol aksi triase per item (icon button, warna netral kecuali "Hapus" bernuansa destructive/merah muda pucat, bukan merah pekat — menghindari kesan menakutkan untuk aksi yang reversibel via soft delete).
- **Empty State:** "Inbox kosong — kerja bagus!" dengan ilustrasi ringan positif, memberi rasa pencapaian saat Inbox benar-benar habis ditriase.
- **Loading State:** Skeleton list saat memuat; submit capture menampilkan spinner kecil di tombol submit saja (bukan blocking seluruh halaman).
- **Error State:** Jika submit capture gagal, teks tetap ada di textarea (tidak hilang) + pesan error ringkas di bawah input (FSD 1.1 Exception Handling).
- **Success State:** Item baru masuk ke daftar dengan animasi slide-in ringan dari atas; item yang ditriase hilang dari daftar dengan animasi fade-out (memberi umpan balik jelas bahwa Inbox "mengosong").
- **Search:** Search lokal sederhana untuk memfilter teks Inbox saat backlog menumpuk (berbeda dari search global — ini scoped ke Inbox saja).
- **Filter:** Filter status (`unprocessed`/`processed`) tersembunyi di belakang toggle "tampilkan riwayat", karena default halaman hanya menampilkan `unprocessed`.
- **Pagination:** Infinite scroll untuk `unprocessed` (biasanya sedikit); pagination standar untuk tampilan riwayat `processed`/`discarded`.
- **Keyboard Shortcut:** `Enter` (dengan fokus di textarea) submit capture; `1`/`2`/`3`/`4` sebagai shortcut aksi triase pada item yang sedang fokus (mempercepat proses triase berturut-turut tanpa mouse).
- **Responsive Behavior:** Tombol aksi triase berubah dari inline hover (desktop) menjadi swipe-action (mobile, pola umum aplikasi to-do), atau baris tombol permanen jika swipe tidak didukung platform.
- **Accessibility:** Setiap tombol aksi memiliki label ARIA eksplisit (bukan hanya ikon); urutan tab mengikuti urutan visual top-to-bottom.
- **Animation:** Item baru: slide-in dari atas (200ms); item ditriase: fade-out + collapse height (250ms) agar tidak ada "lompatan" layout yang mengagetkan.
- **Interaction:** Klik teks item (bukan tombol aksi) membuka mode edit inline sebelum ditriase, untuk mengoreksi typo tanpa harus membuka modal terpisah.
- **UX Rules:** Proses triase harus dapat diselesaikan tanpa berpindah halaman sama sekali (FSD 1.2 — semua aksi berlangsung inline/modal ringan).

## A.3 Halaman: Projects & Goals (List + Detail)

- **Tujuan Halaman:** Struktur menengah–panjang dan progresnya (FSD Modul 3).
- **Layout:** List: grid card (desktop) / list vertikal (mobile). Detail: header ringkasan + tab (Task / Note terkait).
- **Navigation:** Item navigasi primer ketiga (menaungi Goal dan Project dalam satu section, sesuai Information Architecture Blueprint bagian 5).
- **Sidebar:** Sama seperti A.1; pada halaman Detail, sidebar tetap ada (tidak digantikan navigasi Project internal — menjaga akses cepat ke modul lain).
- **Header (List):** Judul "Projects & Goals" + toggle tampilan (Goal view / Project view) + tombol "Goal/Project Baru".
- **Header (Detail):** Nama Project/Goal + badge status + progress bar besar + tombol aksi (Edit, Ubah Status, Fokus jika ada Task aktif).
- **Footer:** Tidak ada.
- **Widget:** Pada Detail Goal: widget agregat seluruh Project di bawahnya dengan mini progress bar per Project.
- **Component:** Progress bar (linear, dengan label persentase); status badge berwarna sesuai status (lihat Design System Badge); Task list item (reuse component dari halaman Tasks).
- **Card:** Card Project/Goal di halaman List menampilkan nama, progress bar mini, jumlah Task aktif, dan tag.
- **Table:** *(Opsional, Could Have)* toggle tampilan tabel untuk power-user yang ingin melihat banyak Project sekaligus dalam bentuk baris data — bukan default.
- **Modal:** Modal "Buat Project/Goal Baru"; modal konfirmasi saat mengubah status ke `completed`/`abandoned` (aksi yang signifikan, layak konfirmasi eksplisit — FSD 3.1/3.2).
- **Form:** Form Create/Edit Project (nama, deskripsi, Goal induk opsional, tag); Form Create/Edit Goal (nama, tipe [immutable setelah simpan — UI mengunci field ini pada mode edit], completion criteria jika berujung).
- **Button:** Primary "Buat Baru"; secondary "Edit"; destructive-muted "Arsipkan" (bukan "Hapus" sebagai aksi utama — mendorong arsip dibanding hapus permanen, sejalan prinsip keandalan data).
- **Empty State (List):** "Belum ada Project/Goal — mulai dari satu tujuan yang ingin Anda capai" + tombol CTA langsung membuat yang pertama.
- **Empty State (Detail — tanpa Task):** "Belum ada Task di Project ini" + CTA tambah Task langsung dari sini.
- **Loading State:** Skeleton card pada List; skeleton header+tab pada Detail.
- **Error State:** Pesan error dengan tombol "Coba Lagi" jika gagal memuat detail Project/Goal.
- **Success State:** Toast konfirmasi setelah Create/Edit berhasil ("Project berhasil dibuat"); progress bar di header animasi mengisi (bukan langsung melompat) saat progres berubah.
- **Search:** Search lokal untuk memfilter List berdasarkan nama.
- **Filter:** Berdasarkan status, tag, dan (untuk Project) Goal induk.
- **Pagination:** Standar (bukan infinite scroll) di halaman List — jumlah Project/Goal biasanya cukup kecil untuk single-user, pagination lebih dapat diprediksi dibanding infinite scroll untuk data yang perlu ditinjau sistematis.
- **Keyboard Shortcut:** `G then P` = ke halaman Projects & Goals.
- **Responsive Behavior:** Grid 3 kolom (desktop) → 2 kolom (tablet) → 1 kolom (mobile) untuk List; tab Detail berubah dari horizontal menjadi dropdown selector di mobile.
- **Accessibility:** Progress bar memiliki `aria-valuenow`/`aria-valuemax`; status badge tidak hanya mengandalkan warna (disertai teks label, bukan warna saja — penting untuk pengguna buta warna).
- **Animation:** Progress bar animasi mengisi halus (400ms ease-out) setiap kali nilai berubah, memberi umpan balik visual kemajuan.
- **Interaction:** Klik Card di List membuka Detail (bukan modal) — Detail cukup kompleks (tab Task+Note) untuk layak halaman penuh, bukan overlay.
- **UX Rules:** Progress bar **tidak pernah** dapat diisi manual oleh user (FSD 3.2 Business Rule) — UI sengaja tidak menyediakan input untuk field ini di form mana pun, murni read-only terhitung otomatis.

## A.4 Halaman: Tasks (All-Tasks View)

- **Tujuan Halaman:** Pandangan lintas-Project atas seluruh Task (FSD Modul 2).
- **Layout:** List vertikal dikelompokkan (grouped) berdasarkan status atau Project, dengan grouping dapat dipilih user.
- **Navigation:** **Tidak memiliki item navigasi primer sendiri** (sesuai keputusan Blueprint bagian 6: Task selalu diakses lewat Dashboard atau Project) — halaman ini diakses via link "lihat semua" dari widget Dashboard.
- **Sidebar:** Sama seperti A.1.
- **Header:** Judul "Semua Task" + kontrol grouping (dropdown: berdasarkan status/Project/prioritas) + tombol "Tambah Task".
- **Footer:** Tidak ada.
- **Widget:** Tidak berlaku.
- **Component:** Task row (checkbox, judul, badge prioritas, badge Project jika ada, badge deadline dengan warna berbeda jika terlambat).
- **Card:** Task disajikan sebagai row dalam list, bukan Card individual — daftar panjang lebih efisien secara vertikal sebagai list densitas sedang, bukan Card besar per item.
- **Table:** *(Opsional Could Have)* mode tabel untuk power-user (kolom: judul, Project, prioritas, deadline, status) — toggle terpisah dari mode list default.
- **Modal:** Modal Quick Edit Task (klik ikon edit pada row, tanpa membuka halaman detail terpisah — Task tidak memiliki halaman detail penuh sendiri karena kompleksitasnya rendah, cukup modal).
- **Form:** Form Create/Edit Task di dalam modal (judul, deskripsi, prioritas, deadline, Project, tag).
- **Button:** Primary "Tambah Task"; inline checkbox sebagai aksi utama penyelesaian (bukan tombol terpisah).
- **Empty State:** Berbeda per filter aktif — mis. filter "Selesai hari ini" kosong → "Belum ada Task selesai hari ini", bukan pesan generik yang sama untuk semua kondisi filter.
- **Loading State:** Skeleton row berulang (5–8 baris placeholder).
- **Error State:** Banner error di atas list dengan tombol muat ulang.
- **Success State:** Toast singkat saat Task berhasil dibuat/diubah; checkbox strikethrough animasi saat selesai.
- **Search:** Search lokal dalam halaman (judul/deskripsi Task).
- **Filter:** Status, prioritas, Project, tag, rentang deadline — filter dapat dikombinasikan (AND, bukan OR, untuk hasil presisi).
- **Pagination:** Infinite scroll dengan grouping tetap terjaga (grup baru muncul saat scroll, bukan pagination bernomor yang memecah grup di tengah halaman).
- **Keyboard Shortcut:** `A` = tambah Task baru dari halaman ini; `/` = fokus ke search lokal.
- **Responsive Behavior:** Badge Project/deadline yang di desktop tampil inline berpindah menjadi baris kedua di bawah judul pada mobile (menghindari teks terpotong).
- **Accessibility:** Checkbox memiliki label yang menyertakan judul Task (bukan hanya "checkbox" generik) untuk pembaca layar; grouping header memiliki heading level yang benar (`<h3>` per grup).
- **Animation:** Task yang difilter keluar dari view (mis. ditandai selesai saat filter aktif "belum selesai") fade-out sebelum benar-benar hilang dari DOM, bukan hilang tiba-tiba.
- **Interaction:** Drag-and-drop *(Future Enhancement)* untuk memindahkan Task antar-Project langsung dari list ini.
- **UX Rules:** Default filter selalu menyembunyikan `done`/`archived` kecuali user eksplisit memilih untuk menampilkannya (FSD 2.2).

## A.5 Halaman: Habits

- **Tujuan Halaman:** Definisi dan tracking konsistensi kebiasaan (FSD Modul 7).
- **Layout:** List Habit, masing-masing dengan mini-grid mingguan (7 kotak) menunjukkan konsistensi.
- **Navigation:** Item navigasi primer keempat.
- **Sidebar:** Sama seperti A.1.
- **Header:** Judul "Habits" + tombol "Habit Baru".
- **Footer:** Tidak ada.
- **Widget:** Tidak berlaku (halaman ini sendiri adalah kumpulan "widget" per Habit).
- **Component:** Habit row (nama + frequency badge + streak counter + grid 7 hari terakhir, kotak terisi/kosong).
- **Card:** Setiap Habit adalah Card ringan dengan grid konsistensi sebagai elemen visual utama.
- **Table:** Tidak digunakan — grid visual lebih sesuai untuk pola konsistensi dibanding tabel data.
- **Modal:** Modal Create/Edit Habit.
- **Form:** Nama, frequency type, frequency target (jika relevan), tag.
- **Button:** Primary "Habit Baru"; checkbox besar per hari pada grid (tap-to-check).
- **Empty State:** "Belum ada kebiasaan yang dilacak" + CTA membuat yang pertama, dengan contoh singkat (mis. "belajar bahasa 10 menit/hari").
- **Loading State:** Skeleton Card per Habit.
- **Error State:** Pesan error per-Card jika gagal memuat riwayat streak Habit tertentu (kegagalan tidak menggagalkan Habit lain).
- **Success State:** Animasi micro-celebration ringan (bukan berlebihan) saat streak mencapai kelipatan tertentu (mis. 7, 30 hari) — memberi penguatan positif tanpa mengganggu (Future Enhancement terkait FSD 7.2).
- **Search:** Search lokal berdasarkan nama Habit.
- **Filter:** Status (`active`/`paused`/`archived`), tag.
- **Pagination:** Tidak diperlukan pada skala single-user (jumlah Habit realistis kecil); tampil semua sekaligus.
- **Keyboard Shortcut:** `H` = fokus ke input Habit baru.
- **Responsive Behavior:** Grid 7 hari tetap horizontal di semua breakpoint (kompresi ukuran kotak, bukan diubah jadi vertikal — pola kalender mingguan lebih dikenali secara horizontal).
- **Accessibility:** Setiap kotak grid memiliki `aria-label` tanggal + status (mis. "Senin, 21 Juli — tercentang"), bukan hanya warna.
- **Animation:** Kotak grid terisi dengan efek "pop" ringan (scale 1→1.1→1, 150ms) saat dicentang.
- **Interaction:** Klik kotak grid untuk hari yang lalu (dalam batas retroaktif FSD 7.2) memungkinkan check-in terlambat langsung dari grid, tanpa perlu form terpisah.
- **UX Rules:** Streak yang reset **tidak ditampilkan dengan warna/frasa yang menghukum** (mis. bukan "Anda gagal") — cukup netral ("Streak dimulai lagi"), sejalan prinsip kesehatan psikologis pengguna dan calm technology.

## A.6 Halaman: Knowledge Base (Notes)

- **Tujuan Halaman:** Arsip referensi dan catatan (FSD Modul 8).
- **Layout:** List (sidebar dalam halaman) + panel konten (mirip aplikasi note-taking dua panel) pada desktop; single-column dengan navigasi back-forward pada mobile.
- **Navigation:** Item navigasi primer kelima.
- **Sidebar:** Sidebar aplikasi utama tetap ada; **ditambah** sidebar sekunder khusus daftar Note di dalam halaman ini (dua tingkat sidebar pada desktop, dapat diterima karena Knowledge Base memang berkarakter seperti aplikasi note-taking).
- **Header:** Search/filter Note di atas daftar Note (sidebar sekunder); pada panel konten: judul Note + status badge + tombol Edit.
- **Footer:** Tidak ada.
- **Widget:** Tidak berlaku.
- **Component:** Note list item (judul + cuplikan singkat + tanggal update); editor konten (rich text ringan/markdown, sesuai TDD — tidak dispesifikasikan library di dokumen ini karena bukan keputusan UI/UX).
- **Card:** Note list item sebagai row ringan (bukan Card besar) di sidebar sekunder.
- **Table:** Tidak digunakan.
- **Modal:** Modal konfirmasi arsip Note (bukan modal utama create — create Note langsung membuka panel kosong siap tulis, meminimalkan friksi).
- **Form:** Form inline di panel konten (judul sebagai heading yang dapat diklik-edit, konten sebagai area tulis langsung — tanpa modal terpisah, mirip pengalaman menulis dokumen).
- **Button:** "Note Baru" (primary, di atas sidebar sekunder); "Tautkan ke Project" (secondary, di panel konten); "Arsipkan" (tertiary).
- **Empty State (belum ada Note dipilih):** Panel konten menampilkan ajakan "Pilih catatan di samping, atau buat yang baru".
- **Empty State (belum ada Note sama sekali):** "Belum ada catatan tersimpan — mulai simpan referensi dan ide belajar Anda di sini".
- **Loading State:** Skeleton list di sidebar sekunder; skeleton paragraf di panel konten.
- **Error State:** Pesan error di panel konten jika gagal memuat isi Note tertentu, list tetap berfungsi normal.
- **Success State:** Auto-save indicator halus ("Tersimpan" muncul sebentar di dekat judul) — bukan tombol "Simpan" manual, mengingat sifat Note sebagai tulisan bebas yang sebaiknya tidak berisiko hilang (FSD 8.1 prinsip keandalan data).
- **Search:** Search lokal di sidebar sekunder (judul + isi Note).
- **Filter:** Tag, status, Project tertaut.
- **Pagination:** Infinite scroll pada sidebar sekunder.
- **Keyboard Shortcut:** `N` = Note baru; `Cmd/Ctrl+S` opsional (meski auto-save, shortcut ini tetap dikenali sebagai "commit" psikologis bagi user yang terbiasa dengan pola tersebut).
- **Responsive Behavior:** Dua panel (desktop) → satu panel dengan transisi push/pop (mobile, list → tap → konten, tombol back kembali ke list).
- **Accessibility:** Editor konten mendukung navigasi keyboard penuh; label ARIA pada tombol tautkan Project.
- **Animation:** Transisi antar-Note di panel konten adalah cross-fade halus (150ms), bukan reload penuh yang terasa kaku.
- **Interaction:** Klik nama Project yang tertaut pada panel konten membuka Project Detail terkait (navigasi silang antar-modul yang konsisten dengan prinsip integrasi Blueprint).
- **UX Rules:** Perubahan konten Note disimpan otomatis (debounced, mis. 1–2 detik setelah berhenti mengetik) — tidak pernah bergantung pada user mengingat untuk menekan tombol simpan.

## A.7 Halaman: Focus Mode

- **Tujuan Halaman:** Eksekusi satu Task tanpa distraksi (FSD Modul 9).
- **Layout:** Full-screen overlay (bukan halaman ber-route terpisah secara konseptual, meski dapat memiliki URL sendiri untuk keperluan refresh) — layout tunggal terpusat.
- **Navigation:** Navigasi primer **disembunyikan sepenuhnya** selama mode aktif, digantikan satu tombol "Keluar" kecil di pojok.
- **Sidebar:** Disembunyikan.
- **Header:** Minimal — hanya nama Task besar di tengah dan (opsional) timer di bawahnya.
- **Footer:** Tidak ada.
- **Widget:** Tidak ada.
- **Component:** Timer opsional (start/pause/reset); tombol besar "Tandai Selesai".
- **Card:** Tidak digunakan — komposisi minimal tanpa bingkai Card, latar polos untuk mengurangi elemen visual yang bersaing dengan fokus.
- **Table:** Tidak berlaku.
- **Modal:** Modal konfirmasi kecil jika user menekan "Keluar" saat timer masih berjalan ("Sesi belum selesai — yakin keluar?") — mencegah keluar tidak sengaja.
- **Form:** Tidak ada form — seluruh interaksi berbasis tombol besar, bukan input teks.
- **Button:** Satu tombol utama besar "Tandai Selesai" (primary, ukuran besar, posisi sentral); tombol timer (secondary, lebih kecil); tombol keluar (tertiary, pojok, ukuran kecil sengaja agar tidak mendominasi perhatian).
- **Empty State:** Tidak berlaku (halaman ini selalu memiliki satu Task aktif sebagai prasyarat).
- **Loading State:** Transisi masuk mode fokus memiliki sedikit jeda visual (fade-to-minimal) agar terasa seperti "masuk ke ruang berbeda", bukan loading teknis biasa.
- **Error State:** Jika Task yang difokuskan ternyata sudah dihapus/berubah (race condition), tampilkan pesan singkat dan kembalikan user ke Dashboard.
- **Success State:** Saat "Tandai Selesai" ditekan, animasi perayaan ringan (bukan berlebihan — konsisten prinsip calm technology) sebelum otomatis keluar dari Focus Mode kembali ke halaman asal.
- **Search:** Tidak berlaku.
- **Filter:** Tidak berlaku.
- **Pagination:** Tidak berlaku.
- **Keyboard Shortcut:** `Esc` = keluar (dengan konfirmasi jika timer berjalan); `Space` = start/pause timer.
- **Responsive Behavior:** Layout pada dasarnya sudah sederhana di semua breakpoint — perbedaan utama hanya ukuran font judul Task (lebih kecil di mobile).
- **Accessibility:** Fokus keyboard otomatis terkunci dalam overlay (focus trap) selama mode aktif, sesuai praktik modal/overlay aksesibel standar.
- **Animation:** Masuk: fade + scale halus dari elemen pemicu (bukan hard-cut); Keluar: fade out simetris.
- **Interaction:** Tidak ada elemen lain yang dapat diklik selain tombol yang disebutkan — mode ini sengaja meminimalkan pilihan interaksi untuk mendukung fokus.
- **UX Rules:** Tidak ada notifikasi lain yang muncul secara visual mengganggu selama Focus Mode aktif (dapat di-queue dan muncul setelah sesi berakhir) — sesuai FSD 9.1 Notification Behavior.

## A.8 Halaman: Review (Daily / Weekly / Monthly)

- **Tujuan Halaman:** Ritual refleksi berkala (FSD Modul 10).
- **Layout:** Header ringkasan metrik (grid kecil angka-angka kunci) + area teks refleksi besar di bawahnya.
- **Navigation:** Item navigasi primer keenam, dengan sub-tab (Daily/Weekly/Monthly) di dalam halaman.
- **Sidebar:** Sama seperti A.1.
- **Header:** Judul sesuai tipe Review + selector periode (mis. navigasi minggu sebelumnya/berikutnya untuk Weekly).
- **Footer:** Tidak ada.
- **Widget:** Widget ringkasan metrik (jumlah Task selesai, streak Habit, progres Goal — ditampilkan sebagai angka besar + label, bukan grafik kompleks pada MVP).
- **Component:** Metric tile (angka + label + ikon kecil); textarea refleksi besar dengan placeholder pertanyaan pemandu (mis. "Apa yang berjalan baik minggu ini?").
- **Card:** Metric tile dibungkus Card kecil dalam grid; area refleksi dalam Card besar terpisah.
- **Table:** Tidak digunakan.
- **Modal:** Tidak diperlukan — seluruh interaksi halaman penuh.
- **Form:** Textarea refleksi (auto-save, sama seperti Note).
- **Button:** Navigasi periode (prev/next, ikon panah); tidak ada tombol "Submit" eksplisit karena auto-save.
- **Empty State:** Periode yang belum memiliki data aktivitas sama sekali → "Belum ada aktivitas tercatat pada periode ini" (FSD 10.2 Edge Case), metric tile menampilkan "–" alih-alih angka 0 yang bisa disalahartikan sebagai performa buruk.
- **Loading State:** Skeleton metric tile + skeleton area teks.
- **Error State:** Metric tile individual yang gagal dimuat menampilkan "–" dengan tooltip error, tidak menggagalkan tile lain (FSD 10.1/10.2).
- **Success State:** Indikator auto-save halus yang sama seperti Note.
- **Search:** Tidak berlaku langsung (catatan refleksi historis dapat ditemukan lewat Modul Search).
- **Filter:** Navigasi periode berfungsi sebagai bentuk "filter" waktu.
- **Pagination:** Tidak berlaku — navigasi periode linear (prev/next), bukan daftar berpaginasi.
- **Keyboard Shortcut:** `[` / `]` = periode sebelumnya/berikutnya.
- **Responsive Behavior:** Grid metric tile 4 kolom (desktop) → 2 kolom (mobile).
- **Accessibility:** Metric tile memiliki teks label yang jelas (bukan hanya angka + ikon) untuk pembaca layar.
- **Animation:** Transisi antar-periode adalah slide horizontal halus (searah tombol yang ditekan — kiri untuk "sebelumnya", kanan untuk "berikutnya"), memberi orientasi spasial waktu yang intuitif.
- **Interaction:** Klik metric tile (mis. "Task selesai: 12") dapat mengarahkan ke halaman Tasks dengan filter periode yang sama diterapkan otomatis (navigasi silang kontekstual).
- **UX Rules:** `snapshot_metrics` yang ditampilkan **tidak pernah** diberi label "real-time" atau ikon refresh — karena secara sengaja dibekukan (FSD 10.2), UI harus mengomunikasikan ini secara implisit lewat tidak adanya elemen "live update" pada halaman ini.

## A.9 Halaman: Settings

- **Tujuan Halaman:** Pengaturan Tag, Notification Preference, dan Data Export/Backup (Blueprint bagian 5).
- **Layout:** Sidebar-tab di dalam halaman (Tags / Notifications / Data) + panel konten per tab.
- **Navigation:** Diakses via ikon terpisah (profil/gear), **bukan** bagian dari navigasi primer 6 item (Blueprint bagian 6 — bukan aktivitas harian).
- **Sidebar:** Sidebar aplikasi utama tetap ada; sidebar-tab sekunder khusus Settings.
- **Header:** Judul tab aktif (mis. "Notification Preferences").
- **Footer:** Tidak ada.
- **Widget:** Tidak berlaku.
- **Component:** Toggle switch (untuk boolean preference), time picker (untuk `habit_reminder_time`), tag list dengan tombol hapus per item.
- **Card:** Setiap grup pengaturan (mis. "Reminder Habit") dalam satu Card dengan penjelasan singkat di bawah judul grup.
- **Table:** Tidak digunakan.
- **Modal:** Modal konfirmasi untuk aksi ireversibel (mis. hapus Tag yang dipakai banyak entitas — menampilkan jumlah entitas terdampak sebelum konfirmasi).
- **Form:** Form preference (toggle + time picker); tidak ada tombol "Simpan" terpisah — setiap perubahan toggle langsung tersimpan (immediate save, konsisten pola auto-save di seluruh aplikasi).
- **Button:** Tombol "Export Data" (secondary, di tab Data) — memicu unduhan file, bukan navigasi.
- **Empty State:** Tab Tags kosong → "Belum ada tag dibuat — tag akan muncul di sini setelah digunakan pertama kali di Task/Project/Note/Habit".
- **Loading State:** Skeleton per Card grup pengaturan.
- **Error State:** Toast error jika perubahan preference gagal tersimpan, dengan opsi "Coba Lagi".
- **Success State:** Micro-feedback (ikon centang singkat muncul di sebelah toggle) saat perubahan berhasil tersimpan.
- **Search:** Search lokal pada tab Tags (jika daftar tag banyak).
- **Filter:** Tidak diperlukan lebih lanjut.
- **Pagination:** Tidak diperlukan (jumlah item pengaturan terbatas).
- **Keyboard Shortcut:** Tidak ada shortcut khusus (halaman non-harian, tidak perlu dioptimasi kecepatan akses).
- **Responsive Behavior:** Tab sekunder berubah dari sidebar vertikal (desktop) menjadi tab horizontal scrollable (mobile).
- **Accessibility:** Toggle switch memiliki `role="switch"` dan `aria-checked` yang benar; time picker dapat dioperasikan penuh via keyboard.
- **Animation:** Toggle switch menggunakan transisi bawaan platform (native-feel), tanpa animasi custom berlebihan.
- **Interaction:** Perubahan preference langsung berefek (tidak perlu refresh/simpan eksplisit).
- **UX Rules:** Halaman ini sengaja dijaga sesederhana mungkin — tidak menjadi tempat "pengaturan lanjutan" yang membengkak, karena filosofi produk mengutamakan default yang baik dibanding banyak opsi konfigurasi (Blueprint bagian 14, prinsip "Flexible structure, opinionated defaults").

---

# BAGIAN B — DESIGN SYSTEM

## B.1 Color System

| Token | Peran | Catatan |
|---|---|---|
| `primary` | Aksi utama (tombol primary, link aktif, highlight navigasi) | Satu warna aksen tunggal — hindari terlalu banyak warna brand bersaing, sejalan prinsip *clarity over density* |
| `neutral` (skala 50–900) | Latar, teks, border | Basis Tailwind `slate`/`zinc` direkomendasikan sebagai starting palette (matang, kontras teruji) |
| `success` | Task selesai, konfirmasi, streak positif | Hijau muted, bukan hijau neon — konsisten nuansa "calm" |
| `warning` | Deadline mendekat, Task in-progress lama tak disentuh | Kuning/oranye muted |
| `danger` | Deadline terlewat, aksi hapus (dipakai jarang & pucat, bukan merah pekat) | Sesuai catatan A.2 — destructive action tidak menakutkan berlebihan karena data reversibel via soft delete |
| `info` | Badge Project/Goal, tag netral | Biru/ungu muted, dipakai untuk elemen informatif non-aksi |

**Alasan palet minim & muted:** aplikasi ini dibuka berkali-kali sepanjang hari — palet yang terlalu saturasi tinggi akan melelahkan mata dalam pemakaian jangka panjang; nuansa muted/desaturasi ringan lebih sesuai untuk *daily driver*.

## B.2 Typography

- **Font family:** Satu sans-serif system-native (mis. `Inter` atau font sistem bawaan OS) untuk kecepatan render dan familiaritas, bukan font dekoratif.
- **Skala tipografi:** `text-xs` (metadata/badge) → `text-sm` (body sekunder) → `text-base` (body utama) → `text-lg`/`text-xl` (judul Card/Section) → `text-2xl`/`text-3xl` (judul halaman) — mengikuti skala default Tailwind tanpa kustomisasi berlebihan.
- **Line-height:** Longgar (`leading-relaxed`) untuk area refleksi/Note (teks panjang dibaca nyaman); normal untuk UI chrome (label, badge).

## B.3 Iconography

- Satu set ikon konsisten (direkomendasikan **Lucide** — cocok dengan ekosistem `lucide-react`/svelte/vue yang juga tersedia sebagai Blade component via package komunitas untuk Livewire).
- Ikon selalu disertai label teks pada aksi penting (bukan icon-only) kecuali di ruang sangat terbatas (mis. tombol triase Inbox mobile) — dan bahkan di situ disertai `aria-label`.

## B.4 Grid

- Container max-width `1280px` (desktop), dengan padding horizontal responsif (`px-4` mobile → `px-8` desktop).
- Grid 12-kolom Tailwind standar sebagai basis; layout dua-panel (Knowledge Base) dan dua-kolom (Dashboard) dibangun di atas grid ini.

## B.5 Spacing

- Skala spacing mengikuti default Tailwind (`4px` base unit: `1,2,3,4,6,8,12,16...`) tanpa token custom tambahan — konsistensi dengan seluruh utility Tailwind yang sudah dikenal luas.
- Spacing antar-Card/widget: `gap-4` (mobile) / `gap-6` (desktop).

## B.6 Radius

- Radius sedang (`rounded-lg`, ~8px) sebagai default Card/Button/Input di seluruh aplikasi — cukup lembut untuk kesan "calm technology" tanpa terlalu membulat seperti aplikasi konsumen playful.
- Radius lebih besar (`rounded-xl`/`rounded-2xl`) khusus pada modal dan Focus Mode overlay untuk membedakan konteks "ruang berbeda".

## B.7 Shadow & Elevation

| Level | Shadow | Dipakai untuk |
|---|---|---|
| 0 (flat) | none | Background halaman, row list |
| 1 | `shadow-sm` | Card widget Dashboard, Card Habit |
| 2 | `shadow-md` | Dropdown, popover |
| 3 | `shadow-lg` | Modal, toast |

**Alasan elevation terbatas 4 level:** terlalu banyak level shadow menciptakan hierarki visual yang membingungkan; 4 level cukup untuk membedakan konteks "menempel di halaman" vs "mengambang di atas halaman".

## B.8 Button Style

- **Primary:** solid, warna `primary`, dipakai maksimal satu per section (satu aksi paling penting).
- **Secondary:** outline/border, dipakai untuk aksi pendukung.
- **Tertiary/Link:** teks polos berwarna `primary`, dipakai untuk aksi rendah-tekanan (mis. "lihat semua").
- **Destructive-muted:** warna `danger` versi pucat, khusus aksi seperti "Arsipkan"/"Hapus" — tidak seagresif tombol destructive konvensional, sejalan sifat data yang reversibel via soft delete.
- Ukuran: `sm` (dalam tabel/row), `md` (default form), `lg` (Focus Mode "Tandai Selesai").

## B.9 Input Style

- Border tipis `neutral-300`, radius `rounded-lg` (konsisten B.6), focus state ring `primary` (bukan border tebal berubah warna drastis — transisi halus).
- Textarea auto-expand untuk Quick Capture dan Note (tidak scrollbar internal kecuali melebihi tinggi maksimum wajar).

## B.10 Card Style

- Padding internal konsisten `p-4`/`p-6` tergantung kepadatan konten; border tipis `neutral-200` **atau** shadow level 1 — pilih salah satu, tidak keduanya sekaligus (menghindari Card terkesan "berat" dengan dua metode pembeda batas sekaligus).

## B.11 Modal Style

- Overlay backdrop semi-transparan gelap (`bg-black/40`); modal container `rounded-xl`, `shadow-lg` (level 3); lebar maksimum disesuaikan konten (form kecil: `max-w-md`; form Project/Goal: `max-w-lg`).
- Modal konfirmasi aksi signifikan (ubah status Goal/Project, hapus Tag terpakai) selalu menampilkan konsekuensi eksplisit dalam teks, bukan hanya "Yakin?" generik.

## B.12 Toast

- Muncul di pojok (mis. kanan-atas desktop, atas-tengah mobile), auto-dismiss 3–4 detik, dengan satu aksi opsional (mis. "Urungkan" untuk aksi yang mendukung undo).
- Maksimal satu toast tampil bersamaan — toast baru menggantikan/antre, tidak menumpuk bertingkat (menjaga *calm technology*).

## B.13 Notification (In-App)

- Badge counter pada ikon lonceng/reminder di header (jumlah reminder belum dibaca); panel dropdown daftar reminder aktif saat diklik, dikelompokkan berdasarkan jenis (Deadline/Habit/Review Ritual — sesuai FSD Modul 6 & 11).

## B.14 Badge

- Dipakai untuk status (Task/Project/Goal/Habit) dan prioritas — selalu disertai teks label, warna hanya sebagai penguat bukan satu-satunya pembeda (aksesibilitas, lihat A.3).
- Bentuk pill (`rounded-full`), ukuran teks `text-xs`, padding kecil.

## B.15 Chip

- Dipakai untuk representasi Tag yang dapat dihapus (dengan tombol "x" kecil) — dibedakan dari Badge (yang read-only status) lewat bentuk sedikit lebih kotak (`rounded-md`) agar tidak tertukar secara visual dengan Badge status.

## B.16 Tag (sebagai elemen visual, bukan entitas database)

- Warna Tag **tidak dikustomisasi per-tag oleh user** pada MVP (menghindari kompleksitas UI color-picker untuk fitur pendukung) — seluruh Tag memakai satu warna netral konsisten (`info` muted), dibedakan lewat teks nama saja.

## B.17 Avatar

- Karena aplikasi single-user pada tahap ini, Avatar **hanya dipakai di Settings/profil** (satu tempat), bukan elemen berulang di banyak halaman — disiapkan strukturnya (ukuran `sm`/`md`/`lg`, fallback inisial nama) untuk transisi multi-user di masa depan (Blueprint bagian 19) tanpa perlu desain ulang.

## B.18 Timeline

- Dipakai pada tampilan riwayat status Task/Project/Goal *(Future Enhancement — Activity Log FSD)* — bentuk garis vertikal dengan titik per event, label waktu relatif (mis. "2 jam lalu").

## B.19 Calendar

- Dipakai pada grid konsistensi Habit (A.5) dalam bentuk mini (7 hari) dan berpotensi diperluas ke tampilan bulan penuh sebagai Future Enhancement (heatmap ala kalender kontribusi, Blueprint bagian 7.2).

## B.20 Dark Mode

- **Direkomendasikan didukung sejak awal** (bukan ditunda) mengingat aplikasi ini realistis dibuka di malam hari (mis. Review harian, capture ide larut malam) — kontras rendah di malam hari penting untuk kenyamanan mata.
- Implementasi via Tailwind `dark:` variant + toggle preference tersimpan per user (bukan hanya mengikuti sistem OS semata, meski default awal dapat mengikuti preferensi sistem).
- Palet dark mode: background `neutral-900`/`neutral-950`, teks `neutral-100`, warna aksen (`primary`/`success`/`danger`) sedikit diturunkan saturasinya agar tidak menyilaukan di latar gelap.

## B.21 Component Rules

1. Satu jenis komponen = satu implementasi Livewire/Blade component yang dipakai ulang lintas halaman (mis. `<x-task-row>` dipakai identik di Dashboard, Tasks, dan Project Detail) — **tidak ada duplikasi styling row Task** di berbagai tempat.
2. Setiap komponen interaktif memiliki state eksplisit yang didesain: default, hover, focus, disabled, loading — didefinisikan sekali di Design System ini, bukan diputuskan ad-hoc per halaman.

## B.22 UI Consistency Rules

1. Pola triase/aksi cepat (icon button + label) yang dipakai di Inbox harus **identik secara visual** dengan pola aksi cepat di halaman lain (Tasks, Habits) — user tidak perlu mempelajari ulang pola interaksi per modul (Blueprint bagian 15, Design Principle #3: *Consistency across modules*).
2. Auto-save (Note, Review, Settings) selalu menggunakan indikator visual yang sama di seluruh aplikasi — tidak ada halaman yang tiba-tiba memakai tombol "Simpan" manual sementara halaman lain auto-save, kecuali dengan alasan eksplisit (Task/Project/Goal memang memakai form submit eksplisit karena perubahannya lebih struktural/berdampak, bukan tulisan bebas).
3. Warna status (Badge) dijaga konsisten maknanya lintas entitas: hijau selalu berarti "selesai/positif", kuning/oranye selalu "perlu perhatian", di Task **maupun** Project **maupun** Goal — tidak ada modul yang memakai skema warna status berbeda sendiri.

---

## Penutup

Dokumen ini melengkapi rangkaian Blueprint v1.0 → FSD → TDD → Database Specification dengan lapisan UI/UX yang presisi, cukup detail untuk langsung dijadikan acuan wireframing di Figma (struktur halaman, state, dan komponen sudah terdefinisi) maupun implementasi frontend Tailwind + Livewire (token warna, spacing, radius, dan aturan komponen sudah eksplisit) — tanpa perlu keputusan desain tambahan yang signifikan di tengah proses membangun antarmuka.
