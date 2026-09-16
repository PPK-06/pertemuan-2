@extends('layouts.app')

@section('title', $project->name)
@section('header', $project->name)
@section('subheader', $project->description ?: 'Detail project dan anggota tim.')
@section('action')
    <a href="{{ route('projects.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 transition font-medium">
        ← Kembali
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Anggota --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide">
                    Anggota ({{ $project->members->count() }})
                </h2>
            </div>
            @if ($project->members->isEmpty())
                <p class="text-gray-400 text-sm italic">Belum ada anggota.</p>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach ($project->members as $member)
                        <div class="flex items-center gap-3 py-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-sm font-bold">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-medium text-gray-800 text-sm truncate">{{ $member->name }}</div>
                                <div class="text-xs text-gray-400 truncate">{{ $member->email }}</div>
                            </div>
                            @if ($member->id === $project->owner_id)
                                <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700">Owner</span>
                            @else
                                <form action="{{ route('projects.members.remove', [$project, $member]) }}" method="POST"
                                      onsubmit="return confirm('Hapus {{ $member->name }} dari project?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="text-xs text-gray-400 hover:text-red-500 transition font-medium">
                                        Hapus
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">Tambah Anggota</h2>
            @if ($users->count())
                <form action="{{ route('projects.members.add', $project) }}" method="POST" class="flex gap-3">
                    @csrf
                    <select name="user_id" required
                            class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }} — {{ $user->email }}</option>
                        @endforeach
                    </select>
                    <button type="submit"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-5 py-2.5 rounded-lg transition shrink-0">
                        Tambah
                    </button>
                </form>
                @error('user_id')
                    <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                @enderror
            @else
                <p class="text-gray-400 text-sm italic">Semua user sudah menjadi anggota.</p>
            @endif
        </div>
    </div>

    {{-- Meta & aksi --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4 text-sm">
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Owner</div>
                <div class="font-medium text-gray-700">{{ $project->owner->name ?? '—' }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Dibuat</div>
                <div class="text-gray-500">{{ $project->created_at->format('d M Y') }}</div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-2">
            <a href="{{ route('projects.edit', $project) }}"
               class="flex items-center justify-center w-full bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-4 py-2.5 rounded-lg transition">
                ✏️ Edit Project
            </a>
            <form action="{{ route('projects.destroy', $project) }}" method="POST"
                  onsubmit="return confirm('Hapus project ini? Tidak bisa dibatalkan.')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center justify-center w-full border border-red-300 text-red-500 hover:bg-red-50 font-semibold text-sm px-4 py-2.5 rounded-lg transition">
                    🗑 Hapus Project
                </button>
            </form>
        </div>
    </div>

</div>
@endsection
