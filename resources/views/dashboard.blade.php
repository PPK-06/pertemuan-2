<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - JARA</title>
    <style>
        body { margin:0; background:#f6f6f4; color:#1c1f26;
               font-family:-apple-system,"Segoe UI",Roboto,sans-serif; padding:40px 24px; }
        .wrap { max-width:640px; margin:0 auto; background:#fff; border:1px solid #d8dbe0; padding:32px; }
        h1 { margin:0 0 16px; font-size:22px; font-weight:600; }
        dl { display:grid; grid-template-columns:120px 1fr; gap:8px 16px; margin:0; font-size:15px; }
        dt { color:#6b7280; }
        dd { margin:0; }
    </style>
</head>
<body>
    <div class="wrap">
        <h1>Halo, {{ auth()->user()->name }}</h1>
        <dl>
            <dt>Email</dt><dd>{{ auth()->user()->email }}</dd>
            <dt>Role</dt><dd>{{ auth()->user()->role }}</dd>
        </dl>
    </div>
</body>
</html>
