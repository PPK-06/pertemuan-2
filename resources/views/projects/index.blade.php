<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>JARA - Projects</title></head>
<body>
<h1>Daftar Project</h1>
@if(session('success'))<p style="color:green">{{ session('success') }}</p>@endif
<a href="{{ route('projects.create') }}">Buat Project</a><hr>
@forelse($projects as $project)
<h2><a href="{{ route('projects.show', $project) }}">{{ $project->name }}</a></h2>
<p>{{ $project->description }}</p>
<p>Owner: {{ $project->owner->name }}</p>
<p>Anggota: {{ $project->members->count() }}</p>
<a href="{{ route('projects.edit', $project) }}">Edit</a>
<form action="{{ route('projects.destroy', $project) }}" method="POST" style="display:inline">
@csrf
@method('DELETE')
<button type="submit">Hapus</button>
</form><hr>
@empty
<p>Belum ada project.</p>
@endforelse
</body></html>

