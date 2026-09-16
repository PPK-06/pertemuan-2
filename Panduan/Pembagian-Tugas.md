# Pembagian Tugas — Elang / Fazl / Ferdy

PM: Dhimas (hanya merge conflict + SRS, tidak coding fitur).
Basis branch: `main` **setelah** merge awal (lihat `Panduan-Merge-PM.md`).
Setiap anggota kerja di branch sendiri, PR ke `main`, PM yang merge.

Keputusan PM §6 di `SRS-Fitur-Tambahan.md` wajib dikunci sebelum mulai
(default: Opsi A, tasks ikut cascade, Blade minimal).

## Elang — FR-01: Buat daftar tugas sebagai pemilik (+ validasi input)

Branch: `feature/tasklist-create` (baru dari `main`).

File yang disentuh (milik Elang, anggota lain jangan sentuh):
- `database/migrations/xxxx_create_task_lists_table.php` (tabel `task_lists` + `task_list_user`, skema §5 SRS)
- `app/Models/TaskList.php` (`$fillable = ['name','description']` saja; relasi `owner`, `members`)
- `app/Http/Requests/StoreTaskListRequest.php` (rules: `name required|string|max:255`, `description nullable|string`; `owner_id` tidak ada di rules)
- `app/Http/Controllers/TaskListController.php` — method `index/create/store/show` saja (`destroy` milik Fazl)
- `resources/views/task-lists/{index,create,show}.blade.php`
- `routes/web.php` — tambah `Route::resource('task-lists', ...)->only([index,create,store,show])` dalam grup `auth`

Aturan:
- `owner_id = auth()->id()` di controller, dalam `DB::transaction()` (koordinasi dengan Fazl untuk pola transaksi yang sama).
- Tidak ada `$request->all()`, tidak ada raw query.
- `store` mengembalikan redirect + flash `success`.

Acceptance (Definition of Done):
- [ ] POST valid → row tercipta, `owner_id == auth id`, redirect detail + flash.
- [ ] `owner_id` di body diabaikan.
- [ ] Guest → 302 login. Validasi gagal → 302 back + error.
- [ ] Test feature `StoreTaskListTest`: create sukses, guest ditolak, validasi gagal.

## Fazl — FR-02: Hapus daftar + keanggotaan (+ atomik NFR-01)

Branch: `origin/Fazl` sudah ada dan masih kosong (identik base) — pakai itu, atau
buat `feature/tasklist-delete` bila `Fazl` sudah terpakai.

File yang disentuh:
- `app/Policies/TaskListPolicy.php` (`delete`: `user->id === taskList->owner_id`) + registrasi policy
- `app/Http/Controllers/TaskListController.php` — method `destroy` saja (koordinasi dengan Elang agar tidak konflik; Elang tidak menyentuh `destroy`)
- `tests/Feature/DeleteTaskListTest.php` (sukses owner, 403 non-owner, 404, guest, rollback)

Aturan:
- `destroy` hanya untuk owner (`authorize('delete', $taskList)` / `abort_unless(..., 403)`), dalam `DB::transaction()` mencakup hapus pivot + hapus daftar.
- Pilih SATU mekanisme hapus pivot: FK `cascadeOnDelete` ATAU `detach()` eksplisit — tulis pilihan di PR.
- Terapkan nasib `tasks` sesuai keputusan PM §6.2 (default cascade).

Acceptance:
- [ ] DELETE owner → daftar + pivot hilang, redirect index + flash.
- [ ] DELETE non-owner → 403, data utuh. Id tidak ada → 404. Guest → 302/401.
- [ ] Test simulasi exception di tengah transaksi → rollback penuh, tidak ada sisa setengah.

## Ferdy — NFR-02 otorisasi + NFR-03 hardening + test keamanan

Branch: `feature/tasklist-authz-security` (baru dari `main`).

File yang disentuh:
- `routes/web.php` — pastikan semua route `task-lists` dalam `middleware('auth')` (jangan ubah route milik Elang/Fazl selain grup middleware; koordinasi saat merge)
- `app/Policies/TaskListPolicy.php` — method `view/create` (lengkapi milik Fazl; jangan duplikat method `delete`, tambah yang kurang)
- `tests/Feature/TaskListSecurityTest.php` (otorisasi + SQL injection payload)
- Review/audit: pastikan tidak ada `DB::raw/whereRaw/$request->all()` pada file fitur; laporkan bila ada

Aturan:
- Standar penolakan: non-owner → 403, guest → 302/401. Jangan ubah `AdminMiddleware` (masih passthrough, di luar scope) kecuali menemukan celah pada route baru.
- Payload uji NFR-03: `' OR '1'='1`, `; DROP TABLE task_lists;--`, `<script>alert(1)</script>` pada `name/description` → tersimpan sebagai string/ditolak validasi, tabel utuh, tidak ada error SQL.

Acceptance:
- [ ] Matrix akses hijau: owner 200/302, non-owner 403, guest 302/401 untuk index/show/store/destroy.
- [ ] Test injection hijau. Grep `whereRaw|DB::raw|selectRaw` pada file fitur → nihil (atau hanya binding `?`).
- [ ] `php artisan test` lolos untuk suite fitur.

## Aturan main bersama (hindari conflict)

1. Satu method satu pemilik di `TaskListController`: Elang (`index/create/store/show`), Fazl (`destroy`), Ferdy tidak edit controller kecuali policy call.
2. `routes/web.php` dan `TaskListPolicy.php` disentuh >1 orang — edit berurutan via PR kecil + PM merge satu per satu; jangan coding paralel di file yang sama dalam waktu bersamaan.
3. Jangan menyentuh file branch lama (`TaskController`, `Project`, `DashboardController`, `AuthController`) — fitur ini standalone sampai merge besar selesai.
4. Setiap PR mencantumkan acceptance checklist yang dicentang + output `php artisan test`.
