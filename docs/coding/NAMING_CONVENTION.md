# NAMING_CONVENTION.md

*(Sumber: TDD bagian 5 & 33 — tabel ini adalah rujukan cepat, jangan menyimpang tanpa alasan kuat)*

| Jenis | Konvensi | Contoh |
|---|---|---|
| Model | Singular, PascalCase | `Task`, `InboxItem` |
| Tabel | Plural, snake_case | `tasks`, `inbox_items` |
| Action | VerbNoun + `Action` | `CompleteTaskAction`, `RecalculateProjectProgressAction` |
| Event | Entity + past tense | `TaskCompleted`, `HabitCheckedIn` |
| Listener | Verb-phrase deskriptif | `RecalculateProjectProgress`, `CancelPendingDeadlineReminder` |
| Enum | Entity + Attribute | `TaskStatus`, `GoalType` |
| Policy | Entity + `Policy` | `TaskPolicy` |
| Form Request | Verb + Entity + `Request` | `StoreTaskRequest`, `UpdateTaskRequest` |
| Livewire Component | Domain-prefixed | `Tasks\TaskList`, `Habits\HabitChecklist` |
| Route name | `{modul}.{aksi}` | `tasks.complete`, `inbox.triage` |
| Namespace | `App\Domain\{Modul}\{Jenis}\{Nama}` | `App\Domain\Tasks\Actions\CompleteTaskAction` |
| Migration file | `{timestamp}_create_{table}_table` atau `_add_{column}_to_{table}_table` | `..._create_tasks_table` |
| Test file (Pest) | `{Entity}{Aspect}Test` | `TaskCompletionTest`, `HabitStreakCalculationTest` |

## Alasan Konsistensi Ini Penting

Solo developer yang kembali ke kode setelah jeda (mis. beberapa minggu tidak menyentuh satu modul) harus dapat **menebak** nama file/class tanpa mencari-cari. Konsistensi penamaan adalah bentuk dokumentasi implisit yang paling murah untuk dirawat — dan yang paling membantu AI Assistant menavigasi codebase tanpa perlu penjelasan berulang.

## Larangan Umum

- Jangan singkat nama secara tidak konsisten (`Proj` vs `Project` vs `Prj`) — selalu gunakan nama entitas penuh.
- Jangan pakai nama generik seperti `Helper`, `Util`, `Manager`, `Handler` tanpa domain prefix yang jelas.
- Jangan campur bahasa Indonesia dan Inggris dalam nama class/method (kode selalu bahasa Inggris; dokumentasi boleh bahasa Indonesia).
