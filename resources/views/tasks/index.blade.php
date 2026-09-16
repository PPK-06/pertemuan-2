@extends('layouts.app')

@section('title', 'Task')
@section('header', '📋 Daftar Task')
@section('subheader', 'Semua task yang kamu buat atau ikuti.')
@section('action')
    <a href="{{ route('tasks.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Buat Task
    </a>
@endsection

@section('content')

{{-- Filter / stats bar --}}
@php
    $total      = $tasks->count();
    $todo       = $tasks->where('status', 'todo')->count();
    $inProgress = $tasks->where('status', 'in_progress')->count();
    $done       = $tasks->where('status', 'done')->count();
@endphp
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
        <div class="text-2xl font-bold text-gray-800">{{ $total }}</div>
        <div class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">Total</div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-gray-200 text-center">
        <div class="text-2xl font-bold text-gray-500">{{ $todo }}</div>
        <div class="text-xs text-gray-500 mt-1 font-medium uppercase tracking-wide">To Do</div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-blue-200 text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $inProgress }}</div>
        <div class="text-xs text-blue-500 mt-1 font-medium uppercase tracking-wide">In Progress</div>
    </div>
    <div class="bg-white rounded-xl p-4 border border-emerald-200 text-center">
        <div class="text-2xl font-bold text-emerald-600">{{ $done }}</div>
        <div class="text-xs text-emerald-500 mt-1 font-medium uppercase tracking-wide">Done</div>
    </div>
</div>

@if($tasks->isEmpty())
    {{-- Empty state --}}
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 py-20 text-center">
        <div class="text-5xl mb-4">📭</div>
        <h3 class="text-lg font-semibold text-gray-700">Belum ada task</h3>
        <p class="text-gray-400 text-sm mt-1 mb-6">Mulai dengan membuat task pertamamu.</p>
        <a href="{{ route('tasks.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
            + Buat Task
        </a>
    </div>
@else
    <div class="space-y-3">
        @foreach($tasks as $task)
        @php
            $overdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';

            $priorityColor = match($task->priority) {
                'high'   => 'bg-red-100 text-red-700',
                'low'    => 'bg-emerald-100 text-emerald-700',
                default  => 'bg-amber-100 text-amber-700',
            };
            $statusColor = match($task->status) {
                'in_progress' => 'bg-blue-100 text-blue-700',
                'done'        => 'bg-emerald-100 text-emerald-700',
                default       => 'bg-gray-100 text-gray-600',
            };
            $statusLabel = match($task->status) {
                'in_progress' => 'In Progress',
                'done'        => 'Done',
                default       => 'To Do',
            };
        @endphp
        <div class="bg-white rounded-xl border border-gray-200 px-5 py-4 flex items-center gap-4 hover:shadow-sm transition
                    {{ $task->status === 'done' ? 'opacity-60' : '' }}">

            {{-- Status toggle cepat --}}
            <form action="{{ route('tasks.status', $task) }}" method="POST">
                @csrf @method('PATCH')
                <input type="hidden" name="status"
                       value="{{ $task->status === 'done' ? 'todo' : ($task->status === 'todo' ? 'in_progress' : 'done') }}">
                <button type="submit"
                    class="w-6 h-6 rounded-full border-2 flex items-center justify-center transition
                           {{ $task->status === 'done'
                                ? 'bg-emerald-500 border-emerald-500 text-white'
                                : 'border-gray-300 hover:border-indigo-400' }}"
                    title="Klik untuk ubah status">
                    @if($task->status === 'done') ✓ @endif
                </button>
            </form>

            {{-- Konten utama --}}
            <div class="flex-1 min-w-0">
                <a href="{{ route('tasks.show', $task) }}"
                   class="font-semibold text-gray-800 hover:text-indigo-600 transition block truncate
                          {{ $task->status === 'done' ? 'line-through text-gray-400' : '' }}">
                    {{ $task->title }}
                </a>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    @if($task->project)
                    <span class="text-xs text-gray-400">📁 {{ $task->project->name }}</span>
                    @endif
                    @if($task->due_date)
                    <span class="text-xs {{ $overdue ? 'text-red-500 font-semibold' : 'text-gray-400' }}">
                        🗓 {{ $task->due_date->format('d M Y') }}
                        @if($overdue) · Overdue @endif
                    </span>
                    @endif
                </div>
            </div>

            {{-- Badge --}}
            <div class="flex items-center gap-2 shrink-0">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $priorityColor }}">
                    {{ ucfirst($task->priority) }}
                </span>
                <span class="text-xs font-semibold px-2.5 py-1 rounded-full {{ $statusColor }}">
                    {{ $statusLabel }}
                </span>
            </div>

            {{-- Aksi --}}
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('tasks.edit', $task) }}"
                   class="text-xs text-gray-400 hover:text-indigo-600 transition font-medium">Edit</a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                      onsubmit="return confirm('Hapus task ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="text-xs text-gray-400 hover:text-red-500 transition font-medium">
                        Hapus
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
@endif

@endsection
