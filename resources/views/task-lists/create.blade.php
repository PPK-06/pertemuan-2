<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Buat Daftar Tugas - JARA</title>
    <style>
        :root { --ink:#1c1f26; --muted:#6b7280; --line:#d8dbe0; --bg:#f6f6f4; --accent:#2f5d50; --danger:#a3352b; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--ink);
               font-family:-apple-system,"Segoe UI",Roboto,sans-serif; padding:40px 24px; }
        .wrap { max-width:560px; margin:0 auto; }
        .card { background:#fff; border:1px solid var(--line); padding:32px; }
        h1 { margin:0 0 4px; font-size:24px; font-weight:600; }
        .sub { margin:0 0 24px; color:var(--muted); font-size:14px; }
        label { display:block; font-size:14px; margin-bottom:6px; }
        input, textarea { width:100%; padding:10px 12px; margin-bottom:4px; border:1px solid var(--line);
                background:#fff; font-size:15px; font-family:inherit; }
        textarea { min-height:110px; resize:vertical; }
        input:focus, textarea:focus { outline:2px solid var(--accent); outline-offset:1px; }
        .field { margin-bottom:16px; }
        .err { color:var(--danger); font-size:13px; }
        button { padding:10px 16px; background:var(--accent); color:#fff; border:0;
                 font-size:14px; font-family:inherit; cursor:pointer; }
        .foot { margin-top:20px; font-size:14px; color:var(--muted); }
        a { color:var(--accent); }
    </style>
</head>
<body>
    <div class="wrap">
        <div class="card">
            <h1>Buat Daftar Tugas</h1>
            <p class="sub">Kamu otomatis jadi pemilik daftar tugas yang kamu buat.</p>

            <form method="POST" action="{{ route('task-lists.store') }}">
                @csrf

                <div class="field">
                    <label for="name">Nama</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required>
                    @error('name') <span class="err">{{ $message }}</span> @enderror
                </div>

                <div class="field">
                    <label for="description">Deskripsi <span class="sub">(opsional)</span></label>
                    <textarea id="description" name="description">{{ old('description') }}</textarea>
                    @error('description') <span class="err">{{ $message }}</span> @enderror
                </div>

                <button type="submit">Simpan</button>
            </form>

            <p class="foot"><a href="{{ route('task-lists.index') }}">&larr; Kembali ke daftar</a></p>
        </div>
    </div>
</body>
</html>
