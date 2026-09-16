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
     * Seed the application's database.
     */
    public function run(): void
    {
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

        $project = Project::create([
            'name' => 'Website JARA',
            'description' => 'Project demo untuk melihat UI project & tim.',
            'owner_id' => $dhimas->id,
        ]);
        $project->members()->syncWithoutDetaching([$dhimas->id, $admin->id]);

        TaskList::create([
            'name' => 'Belanja Mingguan',
            'description' => 'Daftar demo untuk melihat UI daftar tugas.',
            'owner_id' => $dhimas->id,
        ])->members()->attach($dhimas->id);

        Task::create([
            'project_id' => $project->id,
            'created_by' => $dhimas->id,
            'title' => 'Buat halaman login',
            'description' => 'Task demo untuk melihat UI tasks.',
            'priority' => 'high',
            'status' => 'in_progress',
            'due_date' => now()->addDays(3),
        ]);
    }
}
