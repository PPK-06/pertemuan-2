@extends('layouts.app')

@section('title', 'Edit Project')
@section('header', '✏️ Edit Project')
@section('subheader', 'Perbarui informasi project.')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('projects.update', $project) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Project <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" id="name" value="{{ old('name', $project->name) }}" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Deskripsi
                </label>
                <textarea name="description" id="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $project->description) }}</textarea>
                @error('description')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('projects.show', $project) }}"
                   class="text-sm text-gray-500 hover:text-gray-700 transition font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
