<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar Tugas - JARA</title>
    <style>
        :root { --ink:#1c1f26; --muted:#6b7280; --line:#d8dbe0; --bg:#f6f6f4; --accent:#2f5d50; --danger:#a3352b; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--ink);
               font-family:-apple-system,"Segoe UI",Roboto,sans-serif; padding:40px 24px; }
        .wrap { max-width:800px; margin:0 auto; }
        .card { background:#fff; border:1px solid var(--line); padding:32px; }
        h1 { margin:0 0 4px; font-size:24px; font-weight:600; }
        .sub { margin:0 0 24px; color:var(--muted); font-size:14px; }
        .ok { margin:0 0 16px; padding:10px 12px; border:1px solid var(--accent);
              color:var(--accent); font-size:14px; }
        .bar { display:flex; align-items:center; justify-content:space-between;
               gap:16px; margin-bottom:24px; }
        .btn { display:inline-block; padding:10px 16px; background:var(--accent); color:#fff;
               border:0; font-size:14px; font-family:inherit; cursor:pointer; text-decoration:none; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid var(--line); }
        th { color:var(--muted); font-weight:600; }
        .empty { padding:48px 0; text-align:center; color:var(--muted); font-size:14px; }
        .muted { color:var(--muted); }
        a { color:var(--accent); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Daftar Tugas</h1>
            <p class="sub"><a href="/dashboard">&larr; Kembali ke dashboard</a></p>

            @if (session('success'))
                <p class="ok">{{ session('success') }}</p>
            @endif

            <div class="bar">
                <p class="sub" style="margin:0">Daftar tugas yang kamu miliki atau kamu ikuti.</p>
                <a class="btn" href="{{ route('task-lists.create') }}">Buat Daftar Tugas</a>
            </div>

            @if ($taskLists->isEmpty())
                <p class="empty">Belum ada daftar tugas. Buat yang pertama.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Deskripsi</th>
                            <th>Anggota</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskLists as $taskList)
                            <tr>
                                <td>{{ $taskList->name }}</td>
                                <td>
                                    @if ($taskList->description)
                                        {{ Str::limit($taskList->description, 60) }}
                                    @else
                                        <span class="muted">&mdash;</span>
                                    @endif
                                </td>
                                <td>{{ $taskList->members_count }}</td>
                                <td><a href="{{ route('task-lists.show', $taskList) }}">Lihat</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>
