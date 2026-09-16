<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Edit Project</title></head>
<body>
<h1>Edit Project</h1>
<form action="{{ route('projects.update', $project) }}" method="POST">
@csrf
@method('PUT')
<label>Nama</label><br>
<input type="text" name="name" value="{{ $project->name }}" required><br><br>
<label>Deskripsi</label><br>
<textarea name="description">{{ $project->description }}</textarea><br><br>
<button type="submit">Simpan Perubahan</button>
</form><br>
<a href="{{ route('projects.index') }}">Kembali</a>
</body></html>

