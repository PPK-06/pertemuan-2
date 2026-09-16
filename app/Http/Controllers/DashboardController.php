<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $projectName = "Project JARA (Simulasi)";
        $allTasks = Task::all();

        $totalTask = $allTasks->count();
        $todoTask = $allTasks->where('status', 'todo')->count();
        $inProgressTask = $allTasks->where('status', 'in_progress')->count();
        $doneTask = $allTasks->where('status', 'done')->count();

        $progressPercent = $totalTask > 0 ? round(($doneTask / $totalTask) * 100) : 0;

        $upcomingTasks = Task::where('status', '!=', 'done')
            ->where('due_date', '>=', Carbon::now())
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        $overdueTasks = Task::where('status', '!=', 'done')
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
