<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $taskList->name }} - JARA</title>
    <style>
        :root { --ink:#1c1f26; --muted:#6b7280; --line:#d8dbe0; --bg:#f6f6f4; --accent:#2f5d50; --danger:#a3352b; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--ink);
               font-family:-apple-system,"Segoe UI",Roboto,sans-serif; padding:40px 24px; }
        .wrap { max-width:720px; margin:0 auto; }
        .card { background:#fff; border:1px solid var(--line); padding:32px; }
        .card + .card { margin-top:24px; }
        h1 { margin:0 0 4px; font-size:24px; font-weight:600; }
        h2 { margin:0 0 16px; font-size:18px; font-weight:600; }
        .sub { margin:0 0 24px; color:var(--muted); font-size:14px; }
        .ok { margin:0 0 16px; padding:10px 12px; border:1px solid var(--accent);
              color:var(--accent); font-size:14px; }
        .meta { font-size:14px; line-height:1.7; }
        .meta dt { color:var(--muted); }
        .meta dd { margin:0 0 12px; }
        table { width:100%; border-collapse:collapse; font-size:14px; }
        th, td { text-align:left; padding:10px 8px; border-bottom:1px solid var(--line); }
        th { color:var(--muted); font-weight:600; }
        .muted { color:var(--muted); }
        a { color:var(--accent); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            @if (session('success'))
                <p class="ok">{{ session('success') }}</p>
            @endif

            <h1>{{ $taskList->name }}</h1>
            <p class="sub"><a href="{{ route('task-lists.index') }}">&larr; Kembali ke daftar tugas</a></p>

            <dl class="meta">
                <dt>Deskripsi</dt>
                <dd>
                    @if ($taskList->description)
                        {{ $taskList->description }}
                    @else
                        <span class="muted">Tidak ada deskripsi.</span>
                    @endif
                </dd>

                <dt>Pemilik</dt>
                <dd>{{ $taskList->owner->name }}</dd>

                <dt>Dibuat</dt>
                <dd>{{ $taskList->created_at->format('d M Y H:i') }}</dd>
            </dl>
        </div>

        <div class="card">
            <h2>Anggota</h2>

            @if ($taskList->members->isEmpty())
                <p class="muted" style="font-size:14px">Belum ada anggota.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($taskList->members as $member)
                            <tr>
                                <td>{{ $member->name }}</td>
                                <td>{{ $member->email }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</body>
</html>
