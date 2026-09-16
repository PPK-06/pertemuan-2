<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Task;
use App\Models\TaskList;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed demo + dummy yang banyak untuk mengisi dashboard, projects,
     * daftar tugas, dan tasks. Semua user berpassword "password".
     */
    public function run(): void
    {
        // -----------------------------------------------------------------
        // Users: tim inti + tambahan acak
        // -----------------------------------------------------------------
        $admin = User::factory()->create([
            'name' => 'Admin JARA',
            'email' => 'admin@jara.test',
            'role' => 'admin',
        ]);

        $dhimas = User::factory()->create([
            'name' => 'Dhimas',
            'email' => 'dhimas@jara.test',
            'role' => 'user',
        ]);

        $team = collect([
            ['Elang', 'Elang@jara.test'],
            ['Fazl', 'fazl@jara.test'],
            ['Ferdy', 'ferdy@jara.test'],
        ])->map(fn ($t) => User::factory()->create([
            'name' => $t[0],
            'email' => $t[1],
            'role' => 'user',
        ]));

        $others = User::factory(5)->create(['role' => 'user']);

        $allUsers = User::all();
        $teamUsers = collect([$dhimas])->merge($team);

        // -----------------------------------------------------------------
        // Projects (6 dummy)
        // -----------------------------------------------------------------
        $projectsData = [
            ['Website JARA', 'Revamp halaman publik + dashboard internal.'],
            ['Aplikasi Kasir', 'Modul transaksi, stok, dan laporan harian.'],
            ['Sistem Absensi', 'QR check-in, rekap bulanan, dan notifikasi.'],
            ['API Integrasi', 'Gateway pembayaran + webhook pesanan.'],
            ['Mobile Companion', 'Aplikasi pendamping: notif task & approval.'],
            ['Dokumentasi & QA', 'Test plan, bug bash, dan release notes.'],
        ];

        $projects = collect($projectsData)->map(function ($p, $i) use ($teamUsers, $allUsers, $dhimas) {
            $owner = $teamUsers[$i % $teamUsers->count()];
            $project = Project::create([
                'name' => $p[0],
                'description' => $p[1],
                'owner_id' => $owner->id,
            ]);
            // Owner + 2-4 anggota acak (dhimas sering diikutkan biar dashboard ramai).
            $members = $allUsers->where('id', '!=', $owner->id)->random(min(4, $allUsers->count() - 1))->pluck('id')->all();
            if (! in_array($dhimas->id, array_merge([$owner->id], $members)) && fake()->boolean(70)) {
                $members[] = $dhimas->id;
            }
            $project->members()->syncWithoutDetaching(array_unique(array_merge([$owner->id], $members)));

            return $project;
        });

        // -----------------------------------------------------------------
        // TaskLists / Daftar Tugas (8 dummy)
        // -----------------------------------------------------------------
        $taskListsData = [
            ['Belanja Mingguan', 'Kebutuhan rutin tim setiap Senin.'],
            ['Backlog Sprint 12', 'Kandidat task untuk sprint berjalan.'],
            ['Bug Prioritas', 'Temuan QA yang harus segera dibereskan.'],
            ['Riset & Eksplorasi', 'Spike teknologi sebelum implementasi.'],
            ['Konten & Copy', 'Teks UI, email notifikasi, dan panduan.'],
            ['Persiapan Demo', 'Checklist sebelum demo ke stakeholder.'],
            ['Maintenance', 'Tugas berulang: backup, update, monitoring.'],
            ['Ide Next Quarter', 'Parkir ide yang belum terjadwal.'],
        ];

        collect($taskListsData)->each(function ($t, $i) use ($teamUsers, $allUsers, $dhimas) {
            $owner = $teamUsers[$i % $teamUsers->count()];
            $list = TaskList::forceCreate([
                'name' => $t[0],
                'description' => $t[1],
                'owner_id' => $owner->id,
            ]);
            $members = $allUsers->where('id', '!=', $owner->id)->random(fake()->numberBetween(1, 4))->pluck('id')->all();
            $list->members()->syncWithoutDetaching(array_unique(array_merge([$owner->id], $members, [$dhimas->id])));
        });

        // -----------------------------------------------------------------
        // Tasks (40 dummy tersebar: status, prioritas, deadline, anggota)
        // -----------------------------------------------------------------
        $verbs = ['Buat', 'Perbaiki', 'Review', 'Testing', 'Deploy', 'Dokumentasikan', 'Desain', 'Refactor', 'Integrasikan', 'Optimasi'];
        $objects = [
            'halaman login', 'dashboard metrik', 'form tambah project', 'API daftar tugas',
            'tambah anggota task', 'hapus task massal', 'notifikasi deadline', 'halaman detail task',
            'filter status', 'pencarian task', 'avatar anggota', 'progress bar project',
            'laporan mingguan', 'ekspor CSV', 'mode gelap navbar', 'validasi form',
            'middleware admin', 'kebijakan otorisasi', 'seed data demo', 'animasi transisi halaman',
        ];

        for ($i = 1; $i <= 40; $i++) {
            $creator = $teamUsers->random();
            $project = $projects->random();

            // Distribusi status: ~35% todo, ~35% in_progress, ~30% done.
            $roll = fake()->numberBetween(1, 100);
            $status = $roll <= 35 ? 'todo' : ($roll <= 70 ? 'in_progress' : 'done');

            // Deadline: done -> lampau; selain itu campur overdue / upcoming / kosong.
            if ($status === 'done') {
                $due = fake()->dateTimeBetween('-14 days', '-1 day');
            } else {
                $kind = fake()->numberBetween(1, 100);
                $due = $kind <= 30
                    ? fake()->dateTimeBetween('-10 days', '-1 day')   // overdue
                    : ($kind <= 70
                        ? fake()->dateTimeBetween('+1 day', '+14 days') // upcoming
                        : null);
            }

            $task = Task::create([
                'project_id' => $project->id,
                'created_by' => $creator->id,
                'title' => $verbs[array_rand($verbs)].' '.strtolower($objects[array_rand($objects)])." #{$i}",
                'description' => fake()->boolean(70) ? fake()->sentence(12) : null,
                'priority' => fake()->randomElement(['low', 'medium', 'medium', 'high']),
                'status' => $status,
                'due_date' => $due,
            ]);

            // Owner selalu anggota + 0-3 anggota lain; dhimas sering diikutkan.
            $assignees = [$creator->id];
            $extra = $allUsers->where('id', '!=', $creator->id)->random(fake()->numberBetween(0, 3))->pluck('id')->all();
            $assignees = array_merge($assignees, $extra);
            if (! in_array($dhimas->id, $assignees) && fake()->boolean(60)) {
                $assignees[] = $dhimas->id;
            }
            $task->assignees()->syncWithoutDetaching(array_unique($assignees));
        }
    }
}
