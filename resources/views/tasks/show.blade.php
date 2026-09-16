@extends('layouts.app')

@section('title', $task->title)

@section('content')
@php
    $overdue = $task->due_date && $task->due_date->isPast() && $task->status !== 'done';
    $isOwner = $task->created_by === auth()->id();
    $statusColor = match($task->status) {
        'in_progress' => 'bg-blue-50 text-blue-700',
        'done'        => 'bg-emerald-50 text-emerald-700',
        default       => 'bg-amber-50 text-amber-700',
    };
    $statusLabel = match($task->status) {
        'in_progress' => 'Dalam Proses',
        'done'        => 'Selesai',
        default       => 'Pending',
    };
@endphp

<div class="space-y-6">
    <div>
        <a href="{{ route('tasks.index') }}" class="text-sm text-slate-500 hover:text-slate-700 transition font-medium">← Kembali ke Daftar</a>
        <h1 class="text-2xl font-bold text-slate-900 mt-2">{{ $task->title }}</h1>
        <div class="flex gap-2 flex-wrap mt-3">
            <span class="text-xs font-semibold px-3 py-1 rounded-full {{ $statusColor }}">{{ $statusLabel }}</span>
            <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-600">{{ ucfirst($task->priority) }}</span>
            @if($isOwner)
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-indigo-50 text-indigo-700">Owner</span>
            @else
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-slate-100 text-slate-500">Anggota</span>
            @endif
            @if($overdue)
                <span class="text-xs font-semibold px-3 py-1 rounded-full bg-red-50 text-red-600">⚠️ Overdue</span>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Kiri: deskripsi + anggota + status --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Deskripsi</h2>
                @if($task->description)
                    <p class="text-slate-700 text-sm leading-relaxed">{{ $task->description }}</p>
                @else
                    <p class="text-slate-400 text-sm italic">Tidak ada deskripsi.</p>
                @endif
            </div>

            {{-- Anggota task --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">
                    Anggota Task ({{ $task->assignees->count() }})
                </h2>
                @if($task->assignees->isNotEmpty())
                    <div class="divide-y divide-slate-100">
                        @foreach($task->assignees as $assignee)
                            <div class="flex items-center gap-3 py-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                    {{ strtoupper(substr($assignee->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-medium text-slate-800 text-sm truncate">{{ $assignee->name }}</div>
                                    <div class="text-xs text-slate-400 truncate">{{ $assignee->email }}</div>
                                </div>
                                @if($assignee->id === $task->created_by)
                                    <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-50 text-indigo-700">Owner</span>
                                @elseif($isOwner)
                                    <form action="{{ route('tasks.members.remove', [$task, $assignee]) }}" method="POST"
                                          onsubmit="return confirm('Hapus {{ $assignee->name }} dari task ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs text-slate-400 hover:text-red-600 transition font-medium">Hapus</button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-slate-400 text-sm italic">Belum ada anggota.</p>
                @endif

                {{-- Tambah anggota (owner only) --}}
                @if($isOwner)
                    @if($candidates->count())
                        <form action="{{ route('tasks.members.add', $task) }}" method="POST" class="flex gap-3 mt-4 pt-4 border-t border-slate-100">
                            @csrf
                            <select name="user_id" required class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                                @foreach($candidates as $candidate)
                                    <option value="{{ $candidate->id }}">{{ $candidate->name }} — {{ $candidate->email }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium shrink-0">Tambah</button>
                        </form>
                    @else
                        <p class="text-slate-400 text-xs mt-4 pt-4 border-t border-slate-100 italic">Semua user sudah menjadi anggota task ini.</p>
                    @endif
                @endif
            </div>

            {{-- Ubah status (owner + anggota) --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h2 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-3">Ubah Status</h2>
                <div class="flex flex-wrap gap-3">
                    @if($task->status !== 'todo')
                    <form action="{{ route('tasks.status', $task) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="todo">
                        <button class="text-sm font-medium px-4 py-2 rounded-xl border border-slate-200 text-slate-600 hover:bg-slate-50 transition">↩ Pending</button>
                    </form>
                    @endif
                    @if($task->status !== 'in_progress')
                    <form action="{{ route('tasks.status', $task) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="in_progress">
                        <button class="text-sm font-medium px-4 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 transition">🔄 Proses</button>
                    </form>
                    @endif
                    @if($task->status !== 'done')
                    <form action="{{ route('tasks.status', $task) }}" method="POST">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="done">
                        <button class="text-sm font-medium px-4 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 transition">✅ Selesai</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        {{-- Kanan: meta + aksi owner --}}
        <div class="space-y-5">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4 text-sm">
                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Project</div>
                    <div class="font-medium text-slate-700">{{ $task->project->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Owner</div>
                    <div class="font-medium text-slate-700">{{ $task->creator->name ?? '—' }}</div>
                </div>
                <div>
                    <div class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Deadline</div>
                    @if($task->due_date)
                        <div class="font-medium {{ $overdue ? 'text-red-600' : 'text-slate-700' }}">{{ $task->due_date->format('d M Y, H:i') }}</div>
                    @else
                        <div class="text-slate-400 italic">Tidak ada</div>
                    @endif
                </div>
            </div>

            @if($isOwner)
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-2">
                <a href="{{ route('tasks.edit', $task) }}"
                   class="flex items-center justify-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm px-4 py-2.5 rounded-xl transition">✏️ Edit Task</a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST"
                      onsubmit="return confirm('Hapus task ini dari semua anggota? Tidak bisa dibatalkan.')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="flex items-center justify-center w-full border border-red-200 text-red-500 hover:bg-red-50 font-medium text-sm px-4 py-2.5 rounded-xl transition">🗑 Hapus Task</button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
