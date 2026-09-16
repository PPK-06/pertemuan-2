# SRS — Fitur Tambahan Daftar Tugas

PM: Dhimas. Pelaksana: Elang, Fazl, Ferdy.
Sumber requirement: pesan PM (5 poin) + inspeksi branch existing.
Batasan: file `Panduan Pembuatan Part 1.pdf` di root **tidak dapat dibaca mesin**
(model tidak mendukung input PDF), jadi SRS ini hanya mencakup fitur tambahan
yang ditulis eksplisit di pesan. Jika isi PDF memuat requirement lain,
tempel teksnya agar SRS dilengkapi.

## 1. Konteks / Kondisi Existing (hasil inspeksi)

- `main`: hanya `routes/web.php:5-7` (welcome) + PDF. Belum ada fitur.
- `origin/Task-Management`: `TaskController` (CRUD task + `updateStatus`),
  `Task` model, migrasi `tasks` + `task_user`, views `tasks/*`, routes `tasks.resource`.
  **Cacat ditemukan:** controller mereferensi `App\Models\Project` (owner/members),
  tetapi file `app/Models/Project.php` **tidak ada** di branch manapun.
  Keputusan desain di bawah mengasumsikan "Daftar Tugas" = entitas daftar
  (setara `Project` yang hilang) ATAU perlu dibuat baru sebagai `TaskList`.
  PM harus menetapkan salah satu sebelum eksekusi (lihat §2).
- `origin/feature/user-admin`: `AuthController@register`, `Admin/UserController`,
  `AdminMiddleware` (masih passthrough, belum ada cek role), migrasi `role`,
  view `auth/register` + `dashboard`.
- `origin/PnP`: `DashboardController@index` + view `dashboard`.
- `origin/Fazl`: identik dengan base (`commit awal`), belum ada perubahan —
  cocok dijadikan branch kerja Fazl untuk fitur baru.

Konflik merge yang sudah dapat diprediksi (PM yang resolve):
`routes/web.php` (disentuh 3 branch), `resources/views/dashboard.blade.php`
(PnP vs user-admin), `app/Models/User.php` (tambah `role`).

## 2. Definisi

- **Daftar Tugas (TaskList):** wadah tugas yang punya satu **pemilik** (`owner_id → users.id`)
  dan nol/banyak **anggota** via pivot `task_list_user (task_list_id, user_id)`.
- Opsi implementasi (PM pilih satu, default disarankan Opsi A):
  - **Opsi A (disarankan):** buat model+migrasi baru `TaskList` + pivot
    `task_list_user`. Tidak menyentuh migrasi `tasks` existing. `tasks`
    dikaitkan belakangan bila perlu via `task_list_id` nullable.
  - **Opsi B:** hidupkan kembali `Project` yang hilang sebagai "Daftar Tugas".
    Risiko: menimpa asumsi `TaskController` existing (butuh `projects` +
    `project_user` yang belum ada migrasinya).

## 3. Functional Requirements

### FR-01 Membuat daftar tugas baru sebagai pemilik
- User terautentikasi (`auth`) dapat membuat daftar tugas baru.
- `owner_id` **wajib diisi dari `auth()->id()` di server**, tidak pernah dari input client.
- Input minimal: `name` (required, string, max 255). Opsional: `description` (nullable string).
- Response: redirect ke detail daftar + flash `success`; atau 201 JSON bila API.
- Acceptance:
  - [ ] POST dengan data valid → 302/201, row tercipta, `owner_id == auth id`.
  - [ ] `owner_id` dari body diabaikan (tidak mass-assignable).
  - [ ] Guest → redirect login (302) / 401 JSON.

### FR-02 Menghapus daftar tugas beserta seluruh keanggotaan
- Hanya **pemilik** dapat menghapus daftar miliknya.
- Hapus daftar + seluruh baris keanggotaan di `task_list_user`
  (via FK `cascadeOnDelete` ATAU `detach()` eksplisit dalam transaksi — pilih satu,
  jangan dua-duanya setengah).
- Tugas (`tasks`) di dalamnya: PM tetapkan — default disarankan ikut terhapus
  (cascade) atau ditolak bila masih ada tugas (409). Tulis keputusan di sini
  sebelum coding agar Fazl tidak menebak.
- Acceptance:
  - [ ] DELETE oleh owner → daftar + pivot hilang, redirect index + flash.
  - [ ] DELETE oleh non-owner → 403, data utuh.
  - [ ] DELETE id tidak ada → 404.
  - [ ] Guest → 302/401.

## 4. Non-Functional / Constraint Requirements

### NFR-01 Atomik (transaksi)
- Setiap proses tulis (create FR-01, delete FR-02) dibungkus `DB::transaction()`.
  Jika satu langkah gagal (validasi DB, FK, detach), **seluruh perubahan rollback**.
- Acceptance:
  - [ ] Test simulasi kegagalan di tengah (mock/exception) → tidak ada row setengah jadi.
  - [ ] Tidak ada `create`/`delete` + `attach`/`detach` di luar transaksi.

### NFR-02 Otorisasi — request tidak berwenang ditolak
- Semua route daftar tugas di dalam `middleware('auth')`.
- Otorisasi kepemilikan via `TaskListPolicy` (`create`, `delete`) atau
  `abort_unless($taskList->owner_id === auth()->id(), 403)`.
  Gunakan Policy (standar Laravel), bukan cek ad-hoc di tiap method.
- `AdminMiddleware` existing masih passthrough — **di luar scope** fitur ini,
  jangan diubah kecuali Ferdy menemukan celah auth pada route baru.
- Acceptance:
  - [ ] Akses resource milik user lain → 403 (bukan 404/500, bukan redirect sukses).
  - [ ] Test: owner 200/302, non-owner 403, guest 302/401.

### NFR-03 Validasi + parameterisasi (anti SQL injection)
- Seluruh input melalui `FormRequest` (`StoreTaskListRequest`, + request delete
  bila ada body) dengan rules eksplisit. Tidak ada `$request->all()` ke model.
- Query hanya via Eloquent / query builder terparameterisasi. **Larang**
  `DB::raw`, `whereRaw`, `orderByRaw` dengan interpolasi input; bila perlu raw,
  gunakan binding (`?`).
- `$fillable` model hanya `['name','description']` — `owner_id` di-set manual.
- Acceptance:
  - [ ] Input `name` berisi `' OR '1'='1`, `<script>`, `; DROP TABLE` → disimpan
    sebagai string biasa / ditolak validasi, tidak ada error SQL, tabel utuh.
  - [ ] `php artisan test` security case hijau.
  - [ ] Grep `whereRaw|DB::raw|selectRaw` pada file fitur → nihil (atau hanya dengan binding).

## 5. Skema DB yang diusulkan (Opsi A)

```php
Schema::create('task_lists', function (Blueprint $table) {
    $table->id();
    $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
    $table->string('name');
    $table->text('description')->nullable();
    $table->timestamps();
});
Schema::create('task_list_user', function (Blueprint $table) {
    $table->id();
    $table->foreignId('task_list_id')->constrained()->cascadeOnDelete();
    $table->foreignId('user_id')->constrained()->cascadeOnDelete();
    $table->unique(['task_list_id', 'user_id']);
});
```

Relasi `TaskList`: `owner() belongsTo(User)`, `members() belongsToMany(User)`.
Policy: `TaskListPolicy@view/create/delete`.

## 6. Keputusan PM yang masih terbuka (wajib diisi sebelum coding)

1. Opsi A vs Opsi B (§2).
2. Nasib `tasks` saat daftar dihapus: cascade ikut hapus / tolak bila ada tugas.
3. Butuh view Blade (`task-lists/*`) atau cukup redirect/flash? Default: Blade
   minimal (`index`, `create`, `show`) mengikuti pola `views/tasks/*` existing.
4. API JSON diperlukan atau Blade-only?
