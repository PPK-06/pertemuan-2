@extends('layouts.app')

@section('title', 'Daftar Tugas Saya')

@section('content')
<div x-data="{ showForm: false }" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Tugas Saya</h1>
            <p class="text-slate-500 text-sm">Kelola dan pantau progres pekerjaan harianmu di sini.</p>
        </div>
        <button @click="showForm = !showForm" class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium text-sm rounded-xl transition shadow-sm shadow-indigo-200">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Tugas Baru
        </button>
    </div>

    <!-- Quick Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Tugas</p>
                <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $tasks->count() ?? 0 }}</h3>
            </div>
            <div class="p-3 bg-slate-100 text-slate-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Dalam Proses</p>
                <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ $tasks->where('status', 'in_progress')->count() ?? 0 }}</h3>
            </div>
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 9 0 0118 0z"/></svg>
            </div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Selesai</p>
                <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ $tasks->where('status', 'done')->count() ?? 0 }}</h3>
            </div>
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            </div>
        </div>
    </div>

    <!-- Form Input Form Modal / Inline Collapsible -->
    <div x-show="showForm" x-transition class="bg-white border border-slate-200 rounded-2xl p-6 shadow-sm">
        <form action="{{ route('tasks.store') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Project</label>
                <select name="project_id" required class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
                    <option value="">-- Pilih Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Judul Tugas</label>
                <input type="text" name="title" required placeholder="Contoh: Buat laporan mingguan..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm">
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Deskripsi Singkat (Opsional)</label>
                <textarea name="description" rows="2" placeholder="Detail tugas tambahan..." class="w-full px-4 py-2.5 rounded-xl border border-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm"></textarea>
            </div>
            <div class="flex justify-end gap-3">
                <button type="button" @click="showForm = false" class="px-4 py-2 text-slate-600 hover:bg-slate-100 rounded-xl text-sm font-medium">Batal</button>
                <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-medium">Simpan Tugas</button>
            </div>
        </form>
    </div>

    <!-- Task List Container -->
    <div class="bg-white border border-slate-200/80 rounded-2xl shadow-sm overflow-hidden">

        <!-- Filter Bar -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
            <span class="text-sm font-semibold text-slate-700">Daftar Pekerjaan</span>
        </div>

        <!-- Task List Items -->
        <div class="divide-y divide-slate-100">
            @forelse ($tasks as $task)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-slate-50/80 transition group">
                    <div class="flex items-center gap-4">

                        <!-- Toggle Status Form -->
                        <form action="{{ route('tasks.status', $task) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="{{ $task->completed ? 'todo' : 'done' }}">
                            <button type="submit" title="Klik untuk ubah status" class="w-6 h-6 rounded-lg border-2 flex items-center justify-center transition {{ $task->completed ? 'bg-emerald-500 border-emerald-500 text-white' : 'border-slate-300 hover:border-indigo-500' }}">
                                @if($task->completed)
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                @endif
                            </button>
                        </form>

                        <!-- Task Details -->
                        <div>
                            <a href="{{ route('tasks.show', $task) }}" class="font-medium text-sm text-slate-900 hover:text-indigo-600 transition {{ $task->completed ? 'line-through text-slate-400' : '' }}">
                                {{ $task->title }}
                            </a>
                            @if($task->description)
                                <p class="text-xs text-slate-500 mt-0.5">{{ $task->description }}</p>
                            @endif
                            @if($task->project)
                                <p class="text-xs text-slate-400 mt-0.5">📁 {{ $task->project->name }}</p>
                            @endif
                        </div>
                    </div>

                    <!-- Actions & Badge -->
                    <div class="flex items-center gap-3">
                        @if($task->created_by === auth()->id())
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-indigo-50 text-indigo-700">Owner</span>
                        @else
                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full bg-slate-100 text-slate-600">Anggota</span>
                        @endif
                        <span class="px-2.5 py-1 text-xs font-semibold rounded-full {{ $task->completed ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700' }}">
                            {{ $task->completed ? 'Selesai' : ($task->status === 'in_progress' ? 'Proses' : 'Pending') }}
                        </span>

                        <!-- Action Buttons (hanya owner) -->
                        @if($task->created_by === auth()->id())
                        <div class="flex items-center gap-1 sm:opacity-0 sm:group-hover:opacity-100 transition">
                            <a href="{{ route('tasks.edit', $task) }}" class="p-1.5 text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Hapus tugas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus (owner)">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="px-6 py-12 text-center">
                    <div class="w-12 h-12 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <p class="text-slate-500 font-medium text-sm">Belum ada tugas.</p>
                    <p class="text-slate-400 text-xs mt-1">Klik tombol di atas untuk menambahkan tugas baru.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
