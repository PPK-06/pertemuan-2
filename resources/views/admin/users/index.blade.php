<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Users - JARA</title>
    <style>
        :root { --ink:#1c1f26; --muted:#6b7280; --line:#d8dbe0; --bg:#f6f6f4; --accent:#2f5d50; --danger:#a3352b; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--ink);
               font-family:-apple-system,"Segoe UI",Roboto,sans-serif; padding:40px 24px; }
        .wrap { max-width:800px; margin:0 auto; }
        .card { background:#fff; border:1px solid var(--line); padding:32px; }
        .card + .card { margin-top:24px; }
        h1 { margin:0 0 4px; font-size:24px; font-weight:600; }
        h2 { margin:0 0 16px; font-size:18px; font-weight:600; }
        .sub { margin:0 0 24px; color:var(--muted); font-size:14px; }
        label { display:block; font-size:14px; margin-bottom:6px; }
        input, select { width:100%; padding:10px 12px; margin-bottom:4px; border:1px solid var(--line);
                background:#fff; font-size:15px; font-family:inherit; }
        input:focus, select:focus { outline:2px solid var(--accent); outline-offset:1px; }
        .field { margin-bottom:16px; }
        .err { color:var(--danger); font-size:13px; }
        button { padding:10px 16px; background:var(--accent); color:#fff; border:0;
                 font-size:14px; font-family:inherit; cursor:pointer; }
        .danger { background:var(--danger); }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid var(--line); }
        th { color:var(--muted); font-weight:600; }
        a { color:var(--accent); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Manage Users</h1>
            <p class="sub"><a href="/dashboard">&larr; Kembali ke dashboard</a></p>

            @error('user')
                <p class="err">{{ $message }}</p>
            @enderror

            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->role }}</td>
                            <td>
                                @if ($user->id !== auth()->id())
                                    <form method="POST" action="/admin/users/{{ $user->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="danger">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="card">
            <h2>Tambah User</h2>

            <form method="POST" action="/admin/users">
                @csrf

                <div class="field">
                    <label for="name">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                    @error('password') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="user" @selected(old('role') === 'user')>user</option>
                        <option value="admin" @selected(old('role') === 'admin')>admin</option>
                    </select>
                    @error('role') <span class="err">{{ $message }}</span> @enderror
                </div>

                <button type="submit">Simpan</button>
            </form>
        </div>
    </div>
</body>
</html>
