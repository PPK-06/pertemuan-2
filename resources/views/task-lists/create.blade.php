@extends('layouts.app')

@section('title', 'Buat Daftar Tugas')
@section('header', '➕ Buat Daftar Tugas')
@section('subheader', 'Kamu otomatis jadi pemilik daftar tugas yang kamu buat.')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form method="POST" action="{{ route('task-lists.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama <span class="text-red-500">*</span>
                </label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required
                       placeholder="Contoh: Belanja Mingguan"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                              {{ $errors->has('name') ? 'border-red-400 ring-1 ring-red-400' : '' }}">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Deskripsi <span class="text-gray-400 font-normal">(opsional)</span>
                </label>
                <textarea id="description" name="description" rows="3"
                          placeholder="Jelaskan isi daftar tugas ini"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description') }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition">
                    Simpan
                </button>
                <a href="{{ route('task-lists.index') }}"
                   class="text-sm text-gray-500 hover:text-gray-700 transition font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
