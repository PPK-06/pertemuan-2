@extends('layouts.app')

@section('title', 'Daftar Tugas')
@section('header', '🗂️ Daftar Tugas')
@section('subheader', 'Daftar tugas yang kamu miliki atau kamu ikuti.')
@section('action')
    <a href="{{ route('task-lists.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Buat Daftar Tugas
    </a>
@endsection

@section('content')
@if ($taskLists->isEmpty())
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 py-20 text-center">
        <div class="text-5xl mb-4">🗂️</div>
        <h3 class="text-lg font-semibold text-gray-700">Belum ada daftar tugas</h3>
        <p class="text-gray-400 text-sm mt-1 mb-6">Buat wadah pertamamu untuk mengelompokkan tugas.</p>
        <a href="{{ route('task-lists.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
            + Buat Daftar Tugas
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($taskLists as $taskList)
            <a href="{{ route('task-lists.show', $taskList) }}"
               class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md hover:border-indigo-200 transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h3 class="font-semibold text-gray-800 truncate">{{ $taskList->name }}</h3>
                        <p class="text-sm text-gray-400 mt-1 line-clamp-2">
                            {{ $taskList->description ?: 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                    @if ($taskList->owner_id === auth()->id())
                        <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700">Owner</span>
                    @else
                        <span class="shrink-0 text-xs font-semibold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600">Anggota</span>
                    @endif
                </div>
                <div class="flex items-center gap-2 mt-4 text-xs text-gray-400">
                    <span>👥 {{ $taskList->members_count }} anggota</span>
                    <span>·</span>
                    <span>{{ $taskList->created_at->format('d M Y') }}</span>
                </div>
            </a>
        @endforeach
    </div>
@endif
@endsection
