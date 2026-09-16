# Panduan Merge untuk PM (Dhimas)

Tujuan: menggabungkan branch lama yang divergen + branch fitur baru,
dengan PM hanya menangani conflict.

## 1. Urutan merge yang disarankan

1. Kunci keputusan SRS §6 (Opsi A/B, nasib tasks, Blade/API).
2. Merge branch lama ke `main` satu per satu (kecil → besar), resolve conflict, tag:
   a. `origin/PnP` (dashboard, kecil)
   b. `origin/feature/user-admin` (auth + role)
   c. `origin/Task-Management` (task CRUD, besar; catat: referensi `Project` hilang — merge akan membawa controller yang broken, perbaiki dengan Opsi A/B SRS)
3. Dari `main` hasil langkah 2, minta Elang → Fazl → Ferdy branch fitur baru dan PR berurutan.
4. Merge PR fitur satu per satu (Elang dulu, lalu Fazl, lalu Ferdy).

## 2. Perintah dasar

```sh
git checkout main && git pull origin main
git merge origin/PnP
# resolve conflict bila ada, lalu:
git add -A && git commit  # tanpa -m? isi pesan merge yang jelas
git merge origin/feature/user-admin
git merge origin/Task-Management
git push origin main
```

Untuk tiap PR fitur: merge via GitHub PR (squash atau merge commit, pilih satu
dan konsisten), jangan merge lokal + push `--force`.

## 3. Conflict yang sudah diprediksi + cara resolve

| File | Branch bertabrakan | Resolve |
|---|---|---|
| `routes/web.php` | PnP vs user-admin vs Task-Management vs fitur baru | Gabungkan semua grup route; pastikan grup `auth` melingkupi `tasks` + `task-lists` + `dashboard`; grup `guest` hanya register. Uji `php artisan route:list`. |
| `resources/views/dashboard.blade.php` | PnP vs user-admin | Ambil versi terlengkap (biasanya user-admin + konten PnP); cek render `/dashboard` login sebagai user biasa. |
| `app/Models/User.php` | user-admin (tambah `role`) vs base | Pertahankan tambah `role` (`$fillable` + cast). Jangan hapus field base. |
| `app/Http/Controllers/TaskListController.php` | Elang vs Fazl | Elang: `index/create/store/show`; Fazl: `destroy`. Bila conflict, ambil kedua sisi (union), jangan pilih salah satu. |
| `app/Policies/TaskListPolicy.php` | Fazl vs Ferdy | Union: `view/create` (Ferdy) + `delete` (Fazl). |
| `routes/web.php` (fitur) | Elang vs Ferdy | Union: resource routes (Elang) dalam grup `auth` (Ferdy). |

## 4. Checklist pasca-merge (wajib sebelum push)

- [ ] `php artisan migrate:fresh --seed` sukses (atau `migrate` bila DB produksi).
- [ ] `php artisan test` hijau.
- [ ] `php artisan route:list | grep task-list` menunjukkan route dengan middleware `auth`.
- [ ] Login sebagai 2 user berbeda: user A tidak bisa hapus/lihat daftar user B (403).
- [ ] Tidak ada file `Project.php` setengah jadi — putuskan: buat (Opsi B) atau hapus referensinya (Opsi A).
