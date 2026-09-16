$target = Join-Path (Get-Location) "resources\\views\\projects"
New-Item -ItemType Directory -Force -Path $target | Out-Null
@'
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

'@ | Set-Content -Encoding UTF8 (Join-Path $target "index.blade.php")

@'
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
<label>Owner</label><br>
<select name="owner_id" required>
@foreach($users as $user)
<option value="{{ $user->id }}">{{ $user->name }}</option>
@endforeach
</select><br><br>
<button type="submit">Simpan</button>
</form><br>
<a href="{{ route('projects.index') }}">Kembali</a>
</body></html>

'@ | Set-Content -Encoding UTF8 (Join-Path $target "create.blade.php")

@'
<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"><title>{{ $project->name }}</title></head>
<body>
<a href="{{ route('projects.index') }}">Kembali</a>
<h1>{{ $project->name }}</h1>
<p>{{ $project->description }}</p>
<p>Owner: {{ $project->owner->name }}</p>
@if(session('success'))<p style="color:green">{{ session('success') }}</p>@endif
@if(session('error'))<p style="color:red">{{ session('error') }}</p>@endif
<hr><h2>Anggota Project</h2>
@forelse($project->members as $member)
<p>{{ $member->name }} - {{ $member->email }}
@if($member->id == $project->owner_id)
<strong>[Owner]</strong>
@else
<form action="{{ route('projects.members.remove', [$project, $member]) }}" method="POST" style="display:inline">
@csrf
@method('DELETE')
<button type="submit">Hapus</button>
</form>
@endif
</p>
@empty
<p>Belum ada anggota.</p>
@endforelse
<hr><h2>Tambah Anggota</h2>
@if($users->count())
<form action="{{ route('projects.members.add', $project) }}" method="POST">
@csrf
<select name="user_id">
@foreach($users as $user)
<option value="{{ $user->id }}">{{ $user->name }} - {{ $user->email }}</option>
@endforeach
</select>
<button type="submit">Tambah</button>
</form>
@else
<p>Semua user sudah menjadi anggota.</p>
@endif
</body></html>

'@ | Set-Content -Encoding UTF8 (Join-Path $target "show.blade.php")

@'
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

'@ | Set-Content -Encoding UTF8 (Join-Path $target "edit.blade.php")

Write-Host "Selesai: 4 file Blade dibuat di resources\\views\\projects" -ForegroundColor Green
