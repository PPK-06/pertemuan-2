@extends('layouts.app')

@section('title', 'Projects')
@section('header', '📁 Projects')
@section('subheader', 'Kelola project dan anggota tim.')
@section('action')
    <a href="{{ route('projects.create') }}"
       class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Buat Project
    </a>
@endsection

@section('content')
@if ($projects->isEmpty())
    <div class="bg-white rounded-2xl border border-dashed border-gray-300 py-20 text-center">
        <div class="text-5xl mb-4">📁</div>
        <h3 class="text-lg font-semibold text-gray-700">Belum ada project</h3>
        <p class="text-gray-400 text-sm mt-1 mb-6">Buat project pertamamu untuk mulai berkolaborasi.</p>
        <a href="{{ route('projects.create') }}"
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
            + Buat Project
        </a>
    </div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        @foreach ($projects as $project)
            <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm hover:shadow-md transition">
                <a href="{{ route('projects.show', $project) }}"
                   class="font-semibold text-gray-800 hover:text-indigo-600 transition block truncate">
                    {{ $project->name }}
                </a>
                <p class="text-sm text-gray-400 mt-1 line-clamp-2">
                    {{ $project->description ?: 'Tidak ada deskripsi.' }}
                </p>
                <div class="flex items-center gap-2 mt-3 text-xs text-gray-400">
                    <span>👤 {{ $project->owner->name ?? '—' }}</span>
                    <span>·</span>
                    <span>👥 {{ $project->members->count() }} anggota</span>
                </div>
                <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('projects.edit', $project) }}"
                       class="text-xs text-gray-400 hover:text-indigo-600 transition font-medium">Edit</a>
                    <form action="{{ route('projects.destroy', $project) }}" method="POST"
                          onsubmit="return confirm('Hapus project ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-xs text-gray-400 hover:text-red-500 transition font-medium">
                            Hapus
                        </button>
                    </form>
                    <a href="{{ route('projects.show', $project) }}"
                       class="ml-auto text-xs text-indigo-600 hover:text-indigo-700 transition font-semibold">
                        Kelola →
                    </a>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
