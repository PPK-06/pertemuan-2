<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>Buat Project</title></head>
<body>
<h1>Buat Project Baru</h1>
@if($errors->any())
@foreach($errors->all() as $error)<p style="color:red">{{ $error }}</p>@endforeach
@endif
<form action="{{ route('projects.store') }}" method="POST">
@csrf
<label>Nama Project</label><br>
<input type="text" name="name" required><br><br>
<label>Deskripsi</label><br>
<textarea name="description"></textarea><br><br>
<button type="submit">Simpan</button>
</form><br>
<a href="{{ route('projects.index') }}">Kembali</a>
</body></html>

