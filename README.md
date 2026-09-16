# Jara — Task Management App

Aplikasi manajemen tugas kolaboratif berbasis Laravel: project + daftar tugas (task list) + task,
dengan model kepemilikan **owner–anggota**. Owner membuat task/project/daftar tugas, menambahkan
anggota dari user lain, dan hanya owner yang bisa edit/hapus/kelola anggota. Anggota bisa melihat
dan mengubah status.

Dokumen perencanaan tim ada di folder [`Panduan/`](Panduan/) (SRS, pembagian tugas, panduan merge).

## Fitur

- **Auth** — register, login, logout. Role `admin` / `user` (register selalu jadi `user`).
- **Dashboard** — progres + metrik task milik user (owner atau anggota), deadline terdekat, overdue.
- **Projects** — CRUD project; owner dari sesi login; tambah/hapus anggota (owner tidak bisa dihapus).
- **Daftar Tugas (Task Lists)** — buat daftar sebagai pemilik; hapus daftar + keanggotaan atomik (transaksi);
  otorisasi via `TaskListPolicy`.
- **Tasks** — CRUD task; owner = `created_by`; tambah/hapus anggota task; anggota bisa ubah status;
  edit/hapus/kelola anggota khusus owner.
- **Admin** — kelola user (lihat, tambah dengan role, hapus; tidak bisa hapus diri sendiri).
- **UI** — Blade + Tailwind, layout terpadu, brand "Jara", transisi fade antar halaman.

## SRS (ringkas)

Spesifikasi lengkap: [`Panduan/SRS-Fitur-Tambahan.md`](Panduan/SRS-Fitur-Tambahan.md).
Pembagian kerja tim: [`Panduan/Pembagian-Tugas.md`](Panduan/Pembagian-Tugas.md).

| Kode | Requirement |
|------|-------------|
| FR-01 | User login bisa membuat daftar tugas baru; `owner_id` selalu dari `auth()->id()`, tidak pernah dari request body. |
| FR-02 | Hanya pemilik bisa menghapus daftar + seluruh keanggotaannya; non-owner → 403, id tidak ada → 404, guest → redirect login. |
| NFR-01 | Setiap proses tulis atomik (`DB::transaction`): gagal di satu langkah → seluruh perubahan rollback. |
| NFR-02 | Request tidak berwenang ditolak: route di balik `auth`, kepemilikan via Policy / cek owner eksplisit. |
| NFR-03 | Seluruh input divalidasi (`FormRequest` / `$request->validate`), query via Eloquent terparameterisasi — tanpa raw query berinterpolasi (anti SQL injection). |

Aturan owner–anggota task (hasil penyederhanaan setelah merge):

- Owner = `tasks.created_by`. Owner otomatis jadi anggota (`task_user`).
- Owner: CRUD penuh + tambah/hapus anggota + hapus task (hilang dari semua anggota).
- Anggota: lihat + ubah status saja. Non-anggota: 403 dan task tidak muncul di index.

## Tech Stack

| Lapisan | Teknologi |
|---------|-----------|
| Bahasa | PHP 8.5 |
| Framework | Laravel 13.31 |
| Database | SQLite (default lokal, `database/database.sqlite`); migrasi standar, gampang pindah ke MySQL/Postgres via `.env` |
| Frontend | Blade + Tailwind CSS (CDN di layout) + Alpine.js (komponen inline) + Vite build (`resources/css`, `resources/js`) |
| Auth | Session bawaan Laravel + middleware `auth`, `guest`, custom `admin` |
| Testing | Pest 5 + PHPUnit 13 (`tests/Feature`, `tests/Unit`) |
| Dev tools | Laravel Boost, Pail, Pint |

Skema DB utama: `users(role)`, `projects` + `project_user`, `task_lists` + `task_list_user`,
`tasks(project_id, created_by, title, description, priority, status, due_date)` + `task_user`.

## Cara Instalasi

Prasyarat: PHP ≥ 8.3 (dengan ekstensi `pdo_sqlite`), Composer, Node.js + npm.

```bash
git clone <repo-url> Pertemuan2
cd Pertemuan2

composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite

php artisan migrate:fresh --seed   # seed demo: 10 user, 6 project, 8 daftar tugas, 40 task
npm install
npm run build                      # atau: npm run dev (saat ngoding, dengan Vite HMR)
```

Jalankan server:

```bash
php artisan serve   # http://127.0.0.1:8000
```

Akun demo (password semua: `password`):

| Email | Role |
|-------|------|
| `admin@jara.test` | admin |
| `dhimas@jara.test` | user |
| `Elang@jara.test`, `fazl@jara.test`, `ferdy@jara.test` | user |

> Catatan: `database/database.sqlite` dan `.env` tidak di-track git (lihat `.gitignore`),
> jadi tiap clone baru wajib `migrate:fresh --seed`.

## Perintah Penting

```bash
php artisan test            # full test suite (Pest)
php artisan route:list      # daftar route
php artisan migrate:fresh --seed   # reset DB + isi dummy
npm run dev                 # Vite dev server (HMR)
npm run build               # build aset produksi ke public/build
```

## Struktur Kode

```
app/Http/Controllers/   Auth, Dashboard, Project, Task, TaskList, Admin/User
app/Http/Requests/      StoreTaskListRequest (validasi FR-01)
app/Http/Middleware/    AdminMiddleware (cek role admin)
app/Models/             User, Project, Task, TaskList
app/Policies/           TaskListPolicy (view/create/delete)
app/Providers/          AppServiceProvider (registrasi policy)
routes/web.php          semua route (auth, admin, projects, task-lists, tasks)
resources/views/        layouts/{app,guest}, dashboard, tasks/, task-lists/,
                        projects/, auth/, admin/
database/migrations/    10 migrasi (users s/d task_list_user)
database/seeders/       DatabaseSeeder (data dummy tim + 40 task)
tests/Feature/          StoreTaskListTest, TaskListSecurityTest,
                        TaskOwnershipTest, ExampleTest
Panduan/                SRS, pembagian tugas, panduan merge (dokumen tim)
```

## Tim

PM: Dhimas (merge + SRS). Anggota: Elang (FR-01 create), Fazl (FR-02 delete + atomik),
Ferdy (otorisasi + hardening keamanan). Detail: `Panduan/Pembagian-Tugas.md`.
