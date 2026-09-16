@extends('layouts.app')

@section('title', 'Dashboard')
@section('header', 'Dashboard')
@section('subheader', $projectName)

@section('content')
<div class="space-y-6">

    {{-- Progress --}}
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="flex justify-between items-center mb-3">
            <h2 class="font-bold text-gray-800">Project Progress</h2>
            <span class="font-extrabold text-indigo-600">{{ $progressPercent }}%</span>
        </div>
        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-blue-500 h-3 rounded-full transition-all" style="width: {{ $progressPercent }}%"></div>
        </div>
    </div>

    {{-- Metrics --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        <a href="{{ route('tasks.index') }}" class="bg-white rounded-xl p-4 border border-gray-200 text-center hover:shadow-sm transition">
            <div class="text-2xl font-bold text-gray-800">{{ $totalTask }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">Total Tasks</div>
        </a>
        <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
            <div class="text-2xl font-bold text-gray-500">{{ $todoTask }}</div>
            <div class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">To Do</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-blue-200 text-center">
            <div class="text-2xl font-bold text-blue-600">{{ $inProgressTask }}</div>
            <div class="text-xs text-blue-500 mt-1 font-medium uppercase tracking-wide">In Progress</div>
        </div>
        <div class="bg-white rounded-xl p-4 border border-emerald-200 text-center">
            <div class="text-2xl font-bold text-emerald-600">{{ $doneTask }}</div>
            <div class="text-xs text-emerald-500 mt-1 font-medium uppercase tracking-wide">Done</div>
        </div>
    </div>

    {{-- Shortcut --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('projects.index') }}" class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">📁</div>
            <div>
                <div class="font-semibold text-gray-800">Projects</div>
                <div class="text-xs text-gray-400">Kelola project & tim</div>
            </div>
        </a>
        <a href="{{ route('task-lists.index') }}" class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center text-xl">🗂️</div>
            <div>
                <div class="font-semibold text-gray-800">Daftar Tugas</div>
                <div class="text-xs text-gray-400">Wadah & keanggotaan</div>
            </div>
        </a>
        <a href="{{ route('tasks.index') }}" class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition flex items-center gap-4">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center text-xl">✅</div>
            <div>
                <div class="font-semibold text-gray-800">Tasks</div>
                <div class="text-xs text-gray-400">Kerjakan task harian</div>
            </div>
        </a>
    </div>

    {{-- Lists --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Upcoming --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="font-bold text-gray-800 mb-4 flex items-center gap-2">🗓️ Deadline Terdekat</h2>
            @if($upcomingTasks->isEmpty())
                <div class="text-center py-8 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <p class="text-gray-400 text-sm">Tidak ada tugas dengan deadline terdekat.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($upcomingTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="block p-4 rounded-xl border border-gray-100 hover:shadow-sm hover:border-indigo-200 transition">
                            <div class="font-semibold text-gray-800">{{ $task->title ?? 'Untitled Task' }}</div>
                            <div class="text-xs text-gray-400 mt-1">
                                Due: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }}
                                · <span class="capitalize">{{ str_replace('_', ' ', $task->status) }}</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Overdue --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="font-bold text-red-600 mb-4 flex items-center gap-2">⚠️ Tugas Overdue</h2>
            @if($overdueTasks->isEmpty())
                <div class="text-center py-8 bg-emerald-50 rounded-xl border border-dashed border-emerald-200">
                    <p class="text-emerald-600 text-sm font-medium">Bagus! Tidak ada tugas yang overdue.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($overdueTasks as $task)
                        <a href="{{ route('tasks.show', $task) }}" class="block p-4 rounded-xl border border-red-100 bg-red-50/50 hover:shadow-sm transition">
                            <div class="font-semibold text-gray-900">{{ $task->title ?? 'Untitled Task' }}</div>
                            <div class="text-xs text-red-600 mt-1 font-medium">
                                Overdue: {{ \Carbon\Carbon::parse($task->due_date)->diffForHumans() }}
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
