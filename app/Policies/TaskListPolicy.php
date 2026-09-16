<?php

namespace App\Policies;

use App\Models\TaskList;
use App\Models\User;

class TaskListPolicy
{
    /**
     * Hanya owner yang boleh melihat task list miliknya.
     */
    public function view(User $user, TaskList $taskList): bool
    {
        return $user->id === $taskList->owner_id;
    }

    /**
     * Semua user yang sudah login boleh membuat task list baru.
     */
    public function create(User $user): bool
    {
        return true;
    }
}
