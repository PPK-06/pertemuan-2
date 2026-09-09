<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar - JARA</title>
    <style>
        :root { --ink:#1c1f26; --muted:#6b7280; --line:#d8dbe0; --bg:#f6f6f4; --accent:#2f5d50; --danger:#a3352b; }
        * { box-sizing:border-box; }
        body { margin:0; background:var(--bg); color:var(--ink);
               font-family:-apple-system,"Segoe UI",Roboto,sans-serif;
               display:flex; min-height:100vh; align-items:center; justify-content:center; padding:24px; }
        .card { width:100%; max-width:420px; background:#fff; border:1px solid var(--line); padding:32px; }
        h1 { margin:0 0 4px; font-size:24px; font-weight:600; }
        .sub { margin:0 0 24px; color:var(--muted); font-size:14px; }
        label { display:block; font-size:14px; margin-bottom:6px; }
        input { width:100%; padding:10px 12px; margin-bottom:4px; border:1px solid var(--line);
                background:#fff; font-size:15px; font-family:inherit; }
        input:focus { outline:2px solid var(--accent); outline-offset:1px; }
        .field { margin-bottom:16px; }
        .err { color:var(--danger); font-size:13px; }
        button { width:100%; padding:11px; background:var(--accent); color:#fff; border:0;
                 font-size:15px; font-family:inherit; cursor:pointer; }
        .foot { margin-top:20px; font-size:14px; color:var(--muted); text-align:center; }
        a { color:var(--accent); }
    </style>
</head>
<body>
    <div class="card">
        <h1>Buat akun</h1>
        <p class="sub">Daftar untuk mulai mengelola task di JARA.</p>

        <form method="POST" action="/register">
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
                <label for="password_confirmation">Ulangi password</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required>
            </div>

            <button type="submit">Daftar</button>
        </form>

        <p class="foot">Sudah punya akun? <a href="/login">Masuk</a></p>
    </div>
</body>
</html>
