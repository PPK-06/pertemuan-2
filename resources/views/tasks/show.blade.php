@extends('layouts.app')

@section('title', $task->title)
@section('header', $task->title)
@section('subheader', 'Detail lengkap task.')
@section('action')
    <a href="{{ route('tasks.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 transition font-medium">
        ← Kembali ke Daftar
    </a>
@endsection

@section('content')
@php
    $overdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
    $priorityColor = match($task->priority) {
        'high'  => 'bg-red-100 text-red-700',
        'low'   => 'bg-emerald-100 text-emerald-700',
        default => 'bg-amber-100 text-amber-700',
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

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Kolom kiri: info utama --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Card utama --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            {{-- Badges --}}
            <div class="flex gap-2 flex-wrap mb-4">
                <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
                <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $priorityColor }}">{{ ucfirst($task->priority) }} Priority</span>
                @if($overdue)
                    <span class="text-xs font-semibold px-3 py-1 rounded-full bg-red-100 text-red-600">⚠️ Overdue</span>
                @endif
            </div>

            {{-- Deskripsi --}}
            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Deskripsi</h2>
            @if($task->description)
                <p class="text-gray-700 text-sm leading-relaxed">{{ $task->description }}</p>
            @else
                <p class="text-gray-400 text-sm italic">Tidak ada deskripsi.</p>
            @endif
        </div>

        {{-- Assignees --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Assignee</h2>
            @if($task->assignees->isNotEmpty())
                <div class="flex flex-wrap gap-2">
                    @foreach($task->assignees as $assignee)
                        <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-full text-sm font-medium">
                            <div class="w-5 h-5 rounded-full bg-indigo-200 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($assignee->name, 0, 1)) }}
                            </div>
                            {{ $assignee->name }}
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-400 text-sm italic">Belum ada assignee.</p>
            @endif
        </div>

        {{-- Ubah Status --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Ubah Status Cepat</h2>
            <div class="flex flex-wrap gap-3">
                @if($task->status !== 'todo')
                <form action="{{ route('tasks.status', $task) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="todo">
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition">
                        ↩ To Do
                    </button>
                </form>
                @endif
                @if($task->status !== 'in_progress')
                <form action="{{ route('tasks.status', $task) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="in_progress">
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg bg-blue-100 text-blue-700 hover:bg-blue-200 transition">
                        🔄 In Progress
                    </button>
                </form>
                @endif
                @if($task->status !== 'done')
                <form action="{{ route('tasks.status', $task) }}" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="status" value="done">
                    <button class="text-sm font-semibold px-4 py-2 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 transition">
                        ✅ Tandai Selesai
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>

    {{-- Kolom kanan: metadata & aksi --}}
    <div class="space-y-5">

        {{-- Meta --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4 text-sm">
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Project</div>
                <div class="font-medium text-gray-700">{{ $task->project->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Dibuat oleh</div>
                <div class="font-medium text-gray-700">{{ $task->creator->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Deadline</div>
                @if($task->due_date)
                    <div class="font-medium {{ $overdue ? 'text-red-600' : 'text-gray-700' }}">
                        {{ $task->due_date->format('d M Y') }}<br>
                        <span class="text-xs text-gray-400">{{ $task->due_date->format('H:i') }}</span>
                    </div>
                @else
                    <div class="text-gray-400 italic">Tidak ada</div>
                @endif
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Dibuat</div>
                <div class="text-gray-500">{{ $task->created_at->format('d M Y') }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Diperbarui</div>
                <div class="text-gray-500">{{ $task->updated_at->diffForHumans() }}</div>
            </div>
        </div>

        {{-- Aksi --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-2">
            <a href="{{ route('tasks.edit', $task) }}"
               class="flex items-center justify-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition">
                ✏️ Edit Task
            </a>
            <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                  onsubmit="return confirm('Hapus task ini? Tidak bisa dibatalkan.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="flex items-center justify-center w-full border border-red-300 text-red-500 hover:bg-red-50 font-semibold text-sm px-4 py-2.5 rounded-lg transition">
                    🗑 Hapus Task
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
