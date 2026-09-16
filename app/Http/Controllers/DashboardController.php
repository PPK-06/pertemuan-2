<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $projectName = 'Jara';
        // Hanya task milik user (owner atau anggota), konsisten dengan tasks.index.
        $allTasks = Task::where(function ($query) {
                $query->where('created_by', auth()->id())
                    ->orWhereHas('assignees', fn ($q) => $q->where('users.id', auth()->id()));
            })
            ->get();

        $totalTask = $allTasks->count();
        $todoTask = $allTasks->where('status', 'todo')->count();
        $inProgressTask = $allTasks->where('status', 'in_progress')->count();
        $doneTask = $allTasks->where('status', 'done')->count();

        $progressPercent = $totalTask > 0 ? round(($doneTask / $totalTask) * 100) : 0;

        $visible = function ($query) {
            $query->where('created_by', auth()->id())
                ->orWhereHas('assignees', fn ($q) => $q->where('users.id', auth()->id()));
        };

        $upcomingTasks = Task::where($visible)
            ->where('status', '!=', 'done')
            ->where('due_date', '>=', Carbon::now())
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        $overdueTasks = Task::where($visible)
            ->where('status', '!=', 'done')
            ->where('due_date', '<', Carbon::now())
            ->orderBy('due_date', 'asc')
            ->get();

        return view('dashboard', compact(
            'projectName',
            'totalTask',
            'todoTask',
            'inProgressTask',
            'doneTask',
            'progressPercent',
            'upcomingTasks',
            'overdueTasks'
        ));
    }
}
