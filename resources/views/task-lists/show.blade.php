@extends('layouts.app')

@section('title', $taskList->name)
@section('header', $taskList->name)
@section('subheader', 'Detail daftar tugas beserta anggotanya.')
@section('action')
    <a href="{{ route('task-lists.index') }}"
       class="text-sm text-gray-500 hover:text-gray-700 transition font-medium">
        ← Kembali ke daftar
    </a>
@endsection

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Info utama --}}
    <div class="lg:col-span-2 space-y-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-2">Deskripsi</h2>
            @if ($taskList->description)
                <p class="text-gray-700 text-sm leading-relaxed">{{ $taskList->description }}</p>
            @else
                <p class="text-gray-400 text-sm italic">Tidak ada deskripsi.</p>
            @endif
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wide mb-3">
                Anggota ({{ $taskList->members->count() }})
            </h2>
            @if ($taskList->members->isEmpty())
                <p class="text-gray-400 text-sm italic">Belum ada anggota.</p>
            @else
                <div class="flex flex-wrap gap-2">
                    @foreach ($taskList->members as $member)
                        <div class="flex items-center gap-2 bg-indigo-50 text-indigo-700 px-3 py-1.5 rounded-full text-sm font-medium">
                            <div class="w-5 h-5 rounded-full bg-indigo-200 flex items-center justify-center text-xs font-bold">
                                {{ strtoupper(substr($member->name, 0, 1)) }}
                            </div>
                            {{ $member->name }}
                            @if ($member->id === $taskList->owner_id)
                                <span class="text-xs font-bold">· Owner</span>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Meta & aksi --}}
    <div class="space-y-5">
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm space-y-4 text-sm">
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Pemilik</div>
                <div class="font-medium text-gray-700">{{ $taskList->owner->name }}</div>
            </div>
            <div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1">Dibuat</div>
                <div class="text-gray-500">{{ $taskList->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>

        @can('delete', $taskList)
        <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
            <form method="POST" action="{{ route('task-lists.destroy', $taskList) }}"
                  onsubmit="return confirm('Hapus daftar tugas ini beserta seluruh keanggotaannya?')">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="flex items-center justify-center w-full border border-red-300 text-red-500 hover:bg-red-50 font-semibold text-sm px-4 py-2.5 rounded-lg transition">
                    🗑 Hapus Daftar Tugas
                </button>
            </form>
        </div>
        @endcan
    </div>

</div>
@endsection
