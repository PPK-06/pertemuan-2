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

