<?php

namespace App\Policies;

use App\Models\TaskList;
use App\Models\User;

class TaskListPolicy
{
    /**
     * Owner dan anggota boleh melihat daftar tugas.
     */
    public function view(User $user, TaskList $taskList): bool
    {
        if ($user->id === $taskList->owner_id) {
            return true;
        }

        return $taskList->members()->where('users.id', $user->id)->exists();
    }

    /**
     * Semua user yang sudah login boleh membuat task list baru.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Hanya owner yang boleh menghapus daftar tugas miliknya.
     */
    public function delete(User $user, TaskList $taskList): bool
    {
        return $user->id === $taskList->owner_id;
    }
}
