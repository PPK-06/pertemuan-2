@extends('layouts.app')

@section('title', 'Edit Task')
@section('header', '✏️ Edit Task')
@section('subheader', 'Perbarui informasi task.')

@section('content')
<div class="max-w-xl">
    <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <form action="{{ route('tasks.update', $task) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- Project --}}
            <div>
                <label for="project_id" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Project <span class="text-red-500">*</span>
                </label>
                <select name="project_id" id="project_id"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                    <option value="">-- Pilih Project --</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}"
                            {{ old('project_id', $task->project_id) == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Judul --}}
            <div>
                <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Judul Task <span class="text-red-500">*</span>
                </label>
                <input type="text" name="title" id="title"
                       value="{{ old('title', $task->title) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Deskripsi --}}
            <div>
                <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">{{ old('description', $task->description) }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Prioritas & Status --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Prioritas <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" id="priority"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="low"    {{ old('priority', $task->priority) === 'low'    ? 'selected' : '' }}>🟢 Low</option>
                        <option value="medium" {{ old('priority', $task->priority) === 'medium' ? 'selected' : '' }}>🟡 Medium</option>
                        <option value="high"   {{ old('priority', $task->priority) === 'high'   ? 'selected' : '' }}>🔴 High</option>
                    </select>
                    @error('priority') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="status" class="block text-sm font-semibold text-gray-700 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" id="status"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="todo"        {{ old('status', $task->status) === 'todo'        ? 'selected' : '' }}>To Do</option>
                        <option value="in_progress" {{ old('status', $task->status) === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="done"        {{ old('status', $task->status) === 'done'        ? 'selected' : '' }}>Done</option>
                    </select>
                    @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Deadline --}}
            <div>
                <label for="due_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Deadline</label>
                <input type="datetime-local" name="due_date" id="due_date"
                       value="{{ old('due_date', $task->due_date?->format('Y-m-d\TH:i')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-3 pt-2">
                <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm px-6 py-2.5 rounded-lg transition">
                    Simpan Perubahan
                </button>
                <a href="{{ route('tasks.show', $task) }}"
                   class="text-sm text-gray-500 hover:text-gray-700 transition font-medium">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
